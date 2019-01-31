<?php
namespace Setka\Editor\Admin\Service;

use Korobochkin\WPKit\Cron\CronSingleEventInterface;
use Korobochkin\WPKit\Options\OptionInterface;
use Korobochkin\WPKit\PostMeta\PostMetaInterface;
use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Cron;
use Setka\Editor\Plugin;
use Setka\Editor\PostMetas\PostLayoutPostMeta;
use Setka\Editor\PostMetas\PostThemePostMeta;
use Setka\Editor\PostMetas\TypeKitIDPostMeta;
use Setka\Editor\PostMetas\UseEditorPostMeta;
use Setka\Editor\Service\DataFactory;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * We save additional post meta by TWO ways:
 *
 *   1. On POST request when user click "Publish" button.
 *   2. On post auto save events. See the following files for more info:
 *      * wp-includes/js/autosave.js
 *      * wp-includes/js/heartbeat.js
 *      * wp-admin/includes/misc.php:854 (heartbeat_autosave function)
 *
 * Class SavePost
 * @package Setka\Editor\Admin\Service
 */
class SavePost
{
    /**
     * @var OptionInterface
     */
    protected $setkaPostCreatedOption;

    /**
     * @var CronSingleEventInterface
     */
    protected $setkaPostCreatedCronEvent;

    /**
     * @var PostMetaInterface
     */
    protected $useEditorPostMeta;

    /**
     * @var PostMetaInterface
     */
    protected $postThemePostMeta;

    /**
     * @var PostMetaInterface
     */
    protected $postLayoutPostMeta;

    /**
     * @var PostMetaInterface
     */
    protected $typeKitIDPostMeta;

    /**
     * SavePost constructor.
     * @param OptionInterface $setkaPostCreatedOption
     * @param CronSingleEventInterface $setkaPostCreatedCronEvent
     * @param PostMetaInterface $useEditorPostMeta
     * @param PostMetaInterface $postThemePostMeta
     * @param PostMetaInterface $postLayoutPostMeta
     * @param PostMetaInterface $typeKitIDPostMeta
     */
    public function __construct(
        OptionInterface $setkaPostCreatedOption,
        CronSingleEventInterface $setkaPostCreatedCronEvent,
        PostMetaInterface $useEditorPostMeta,
        PostMetaInterface $postThemePostMeta,
        PostMetaInterface $postLayoutPostMeta,
        PostMetaInterface $typeKitIDPostMeta
    ) {
        $this->setkaPostCreatedOption    = $setkaPostCreatedOption;
        $this->setkaPostCreatedCronEvent = $setkaPostCreatedCronEvent;
        $this->useEditorPostMeta         = $useEditorPostMeta;
        $this->postThemePostMeta         = $postThemePostMeta;
        $this->postLayoutPostMeta        = $postLayoutPostMeta;
        $this->typeKitIDPostMeta         = $typeKitIDPostMeta;
    }

    /**
     * Save post meta. This method handles only POST requests.
     *
     * WARNING: this method don't include any checks of current_user_can()
     * or nonce validation because this already happened in edit_post()
     *
     * @see \Setka\Editor\Plugin::runAdmin()
     * @see edit_post()
     * @see wp_update_post()
     * @see wp_insert_post()
     *
     * @since 0.0.2
     *
     * @param $postId int Post ID.
     * @param $post object WordPress Post object
     * @param $update bool Update or create new post.
     *
     * @return $this For chain calls.
     */
    public function postAction($postId, $post, $update)
    {
        // Nonce already validated in wp-admin/post.php

        // Stop on autosave (see heartbeat_received() in this class for autosavings)
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $this;
        }

        // Prevent quick edit from clearing custom fields
        if(defined('DOING_AJAX') && DOING_AJAX) {
            return $this;
        }

        if(defined('REST_REQUEST') && REST_REQUEST) {
            return;
        }

        // Our settings presented in request?
        if(!isset($_POST[Plugin::NAME . '-settings'])) { // WPCS: CSRF ok; Input var okay.
            return $this;
        }

        // Parse settings from JSON object
        // WordPress use addslashes() in wp_magic_quotes()
        $settings = stripcslashes($_POST[Plugin::NAME . '-settings']); // WPCS: sanitization ok; CSRF ok; Input var okay.
        $settings = json_decode($settings, true);

        $this->proceeding($settings, $postId);

        return $this;
    }

    /**
     * Handles default post auto saves (triggering by WordPress Heartbeat API).
     *
     * @see wp_ajax_heartbeat()
     * @see heartbeat_autosave()
     *
     * @since 0.0.2
     *
     * @param $response array Which will be sent back to the client in browser.
     * @param $data array The data comes from JavaScript (Browser).
     *
     * @return array Just pass $response for the next filters as is.
     */
    public function heartbeatReceived($response, $data)
    {
        // Our settings presented in request?
        if(isset($data[Plugin::NAME])) {
            // Create a link to long named variable :) just to write less code below
            $settings =& $data[Plugin::NAME];

            /**
             * @see heartbeat_autosave()
             * @see wp_autosave()
             */

            if(isset($settings['postId'])) {
                $settings['postId'] = (int) $settings['postId'];

                if(isset($settings['_wpnonce'])) {
                    // Check nonce like in heartbeat_autosave()
                    if (false === wp_verify_nonce($settings['_wpnonce'], 'update-post_' . $settings['postId'])) {
                        // Just pass $response for the next filters.
                        return $response;
                    }

                    // Check current_user_can edit post
                    $post = get_post($settings['postId']);
                    if($post instanceof \WP_Post && property_exists($post, 'ID')) {
                        if (current_user_can('edit_post', $post->ID)) {
                            $this->proceeding($settings, $settings['postId']);
                        }
                    }
                }
            }
        }
        // Just pass $response for the next filters.
        return $response;
    }

    /**
     * Simply saves the post meta. Called from heartbeat_received() or from save_post().
     *
     * We need save some extra settings from our Grid Editor (layout style, theme name,
     * the number of cols...) as post meta. Currently we save three things here:
     *
     *   1. Post created with Grid Editor or not (default WP editor).
     *   2. Post layout.
     *   3. Post theme.
     *
     * @since 0.0.2
     *
     * @param $new_settings array Post settings.
     * @param $post_id int Post id.
     *
     * @return $this For chain calls.
     */
    public function proceeding($settings, $post_id)
    {

        /**
         * Possible additional checks:
         *   1. Post Type (post, page, attachment). Currently this not validates because
         *      post may already created with editor and now this post_type disabled but
         *      old post need to be available with editor.
         *
         *   2. Current user can use grid editor. Possible issue then user can edit post
         *      but don't have editor access.
         *
         * At now use only current_user_can('edit').
         */

        if(!isset($settings['useSetkaEditor'])) {
            return $this;
        }

        if(!in_array($settings['useSetkaEditor'], array('0', '1'), true)) {
            return $this;
        }

        // Transform useSetkaEditor after which is string (was sent as POST data).
        if('1' === $settings['useSetkaEditor']) {
            $settings['useSetkaEditor'] = true;
        } else {
            $settings['useSetkaEditor'] = false;
        }

        // Check for the first Setka Editor Post on this site
        if($settings['useSetkaEditor']) {
            if(!$this->setkaPostCreatedOption->get()) {
                $this->setkaPostCreatedCronEvent->schedule();
                $this->setkaPostCreatedOption->updateValue(true);
            }
        }

        try {
            // Post created with Setka Editor or not.
            $this->useEditorPostMeta
                ->setPostId($post_id)
                ->set($settings['useSetkaEditor'])
                ->flush();

            $this->postThemePostMeta->setPostId($post_id);
            // Update Post Theme name. Example: 'village-2016'.
            if(isset($settings['editorConfig']['theme'])) {
                $this->postThemePostMeta->set($settings['editorConfig']['theme']);

                if($this->postThemePostMeta->isValid()) {
                    $this->postThemePostMeta->flush();
                }
            }

            $this->postLayoutPostMeta->setPostId($post_id);
            // Update Post Layout. Example: '6' or '12'.
            if(isset($settings['editorConfig']['layout'])) {
                $this->postLayoutPostMeta->set($settings['editorConfig']['layout']);

                if($this->postLayoutPostMeta->isValid()) {
                    $this->postLayoutPostMeta->flush();
                }
            }

            $this->typeKitIDPostMeta->setPostId($post_id);
            // Update Type Kit ID. Example: 'ktz3nwg'.
            if(isset($settings['editorConfig']['typeKitId'])) {
                $this->typeKitIDPostMeta->set($settings['editorConfig']['typeKitId']);

                if($this->typeKitIDPostMeta->isValid()) {
                    $this->typeKitIDPostMeta->flush();
                }
            } else {
                $this->typeKitIDPostMeta->delete();
            }
        } finally {
            $this->useEditorPostMeta
                ->setPostId(null)
                ->deleteLocal();

            $this->postThemePostMeta
                ->setPostId(null)
                ->deleteLocal();

            $this->postLayoutPostMeta
                ->setPostId(null)
                ->deleteLocal();

            $this->typeKitIDPostMeta
                ->setPostId(null)
                ->deleteLocal();
        }

        return $this;
    }
}

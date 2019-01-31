<?php
namespace Setka\Editor\Service;

use Korobochkin\WPKit\Pages\PageInterface;
use Korobochkin\WPKit\ScriptsStyles\AbstractScriptsStyles;
use Korobochkin\WPKit\ScriptsStyles\ScriptsStylesInterface;
use Setka\Editor\Admin\Notices\NoticesStack;
use Setka\Editor\Admin\Service\Js\EditorAdapterJsSettings;
use Setka\Editor\Plugin;
use Setka\Editor\PostMetas\TypeKitIDPostMeta;
use Setka\Editor\PostMetas\UseEditorPostMeta;
use Setka\Editor\Service\AMP\AMP;
use Setka\Editor\Service\Config\AMPConfig;
use Setka\Editor\Service\Config\PluginConfig;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;

/**
 * Class ScriptStyles
 */
class ScriptStyles extends AbstractScriptsStyles implements ScriptsStylesInterface
{
    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * @var bool True if Gutenberg available and should be used.
     */
    protected $gutenbergSupport;

    /**
     * @var AMP
     */
    protected $amp;

    /**
     * @var \WP_Query
     */
    protected $query;

    /**
     * @var PageInterface
     */
    protected $pluginSettingsPage;

    /**
     * @var EditorGutenbergModule
     */
    protected $editorGutenbergModule;

    /**
     * @var array List of script names with async attr.
     */
    protected $asyncScripts = array();

    /**
     * @var bool Should plugin manage Type Kits or not.
     */
    protected $manageTypeKit = true;

    /**
     * @var array List of Type Kit ID.
     */
    protected $typeKits = array();

    /**
     * @var EditorAdapterJsSettings
     */
    protected $editorAdapterJsSettings;

    /**
     * @var NoticesStack
     */
    protected $noticesStack;

    /**
     * This function register most of CSS and JS files for plugin. It's just registered, not enqueued,
     * so we (or someone else) can enqueue this files only by need. Fired (attached) to `wp_enqueue_scripts` action
     * in \Setka\Editor\Plugin::run().
     *
     * @since 0.0.1
     *
     * @see \Setka\Editor\Plugin::run()
     */
    public function register()
    {
        $prefix  = Plugin::NAME . '-';
        $url     = $this->getBaseUrl();
        $version = Plugin::VERSION;
        $debug   = $this->isDev();

        // URI JS
        wp_register_script(
            'uri-js',
            $url . 'assets/js/uri-js/' . ( ($debug) ? 'URI.js' : 'URI.min.js' ),
            array(),
            '1.18.1',
            true
        );

        // Setka Editor JS
        wp_register_script(
            $prefix . 'editor',
            $this->getSetkaEditorAccount()->getEditorJSOption()->get(),
            array(),
            $version,
            true
        );

        // Setka Editor CSS
        wp_register_style(
            $prefix . 'editor',
            $this->getSetkaEditorAccount()->getEditorCSSOption()->get(),
            array(),
            $version
        );

        wp_register_style($prefix . 'amp', false); // See enqueueing AMP styles below.

        return $this;
    }

    /**
     * @return $this For chain calls.
     */
    public function registerThemeResources()
    {
        $prefix  = Plugin::NAME . '-';
        $version = Plugin::VERSION;
        $local   = $this->getSetkaEditorAccount()->isLocalFilesUsage();

        // Theme CSS
        if($local) {
            $option = $this->getSetkaEditorAccount()->getThemeResourceCSSLocalOption();
        } else {
            $option = $this->getSetkaEditorAccount()->getThemeResourceCSSOption();
        }
        wp_register_style(
            $prefix . 'theme-resources',
            $option->get(),
            array(),
            $version
        );

        // Theme Plugins JS
        wp_register_script(
            $prefix . 'theme-plugins',
            $this->getSetkaEditorAccount()->getThemePluginsJSOption()->get(),
            array('jquery'),
            $version,
            true
        );
        $this->asyncScripts[] = $prefix . 'theme-plugins';

        return $this;
    }

    /**
     * @return $this For chain calls.
     */
    public function registerGutenberg()
    {
        wp_register_script(
            'setka-editor-wp-admin-gutenberg-modules',
            $this->getBaseUrl() . 'assets/js/build/gutenberg-modules.bundle.js',
            array('wp-blocks', 'wp-element'),
            Plugin::VERSION,
            true
        );

        wp_register_style(
            'setka-editor-wp-admin-gutenberg-modules',
            $this->getBaseUrl() . 'assets/js/build/gutenberg-styles.css',
            array(),
            Plugin::VERSION
        );

        register_block_type(
            'setka-editor/setka-editor',
            array(
                'editor_script' => 'setka-editor-wp-admin-gutenberg-modules',
                'editor_style' => 'setka-editor-wp-admin-gutenberg-modules',
                'render_callback' => array($this->editorGutenbergModule, 'render'),
            )
        );

        return $this;
    }

    /**
     * Enqueue scripts and styles for Gutenberg edit post page.
     *
     * @return $this For chain calls.
     */
    public function enqueueForGutenberg()
    {
        wp_enqueue_script('setka-editor-editor');
        wp_enqueue_style('setka-editor-editor');
        wp_enqueue_style('setka-editor-theme-resources');

        return $this;
    }

    /**
     * Localise Gutenberg modules.
     *
     * @return $this For chain calls.
     */
    public function localizeGutenbergBlocks()
    {
        wp_localize_script(
            'setka-editor-wp-admin-gutenberg-modules',
            'setkaEditorGutenbergModules',
            array(
                'name' => Plugin::NAME,
                'settings' => $this->getEditorAdapterJsSettings()->getSettings(),
                'settingsUrl' => $this->pluginSettingsPage->getURL(),
                'notices' => $this->noticesStack->getNoticesAsArray(),
            )
        );

        wp_set_script_translations('setka-editor-wp-admin-gutenberg-modules', Plugin::NAME);

        return $this;
    }

    /**
     * Register Type Kit styles.
     *
     * @return $this For chain calls.
     */
    public function registerTypeKits()
    {
        $prefix = Plugin::NAME . '-type-kit-';

        foreach($this->typeKits as $idKey => $idValue) {
            $idKey = esc_attr($idKey);
            wp_register_style(
                $prefix . $idKey,
                '//use.typekit.net/' . $idKey . '.css'
            );
        }

        return $this;
    }

    /**
     * Enqueue resources if they required for this page.
     *
     * Function fired on wp_enqueue_scripts action.
     *
     * @see \Setka\Editor\Plugin::run()
     *
     * @return $this For chain calls.
     */
    public function enqueue()
    {
        if($this->isAmp()) {
            if($this->query->is_singular()) {
                $this->enqueueAmp();
            }
            return $this;
        }

        if ($this->getSetkaEditorAccount()->isThemeResourcesAvailable() && $this->isResourcesRequired()) {
            $this->enqueueResourcesScriptStyles();
        }

        return $this;
    }

    /**
     * Register Type Kit fonts and enqueue it.
     *
     * @return $this For chain calls.
     */
    public function footer()
    {
        if(!$this->isAmp()) {
            if(!empty($this->typeKits)) {
                $this->registerTypeKits();
                $prefix = Plugin::NAME . '-type-kit-';

                foreach ($this->typeKits as $typeKit => $typeKitValue) {
                    wp_enqueue_style($prefix . $typeKit);
                }
            }
        }
        return $this;
    }

    /**
     * Enqueue CSS for AMP pages with Setka posts.
     *
     * By default this method runs only on singular posts pages but also
     * could be run on any archives pages.
     *
     * @return $this For chain calls.
     */
    public function enqueueAmp()
    {
        if(is_array($this->query->posts)) {
            foreach ($this->query->posts as $post) {
                /**
                 * @var $post \WP_Post
                 */
                $this->amp->setupThemeAndLayoutFromPostMeta($post);
                if($this->isGutenbergSupport()) {
                    do_blocks($post->post_content);
                }
            }
        }

        $this->amp->setupCSS()->setupFonts();

        wp_enqueue_style(Plugin::NAME . '-amp');
        wp_add_inline_style(Plugin::NAME . '-amp', $this->amp->getCss());

        $fonts = $this->amp->getFonts();
        foreach ($fonts as $fontName => $fontUrl) {
            wp_enqueue_style($fontName, $fontUrl);
        }

        return $this;
    }

    /**
     * Check if we should handle request as AMP.
     *
     * @return bool True if AMP page requested and we should use AMP styles.
     */
    public function isAmp()
    {
        $mode = $this->amp->getMode();
        if($this->amp->isAmpSupport()
            &&
            ('paired' === $mode || 'native' === $mode)
            &&
            AMPConfig::isAMPEndpoint()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if some posts (page or any other custom post types) in the loop created with Setka Editor.
     *
     * Additionally registering Type Kits if post theme using this type of fonts.
     *
     * @see UseEditorPostMeta
     *
     * @return bool Returns true if at least one post created with Setka Editor.
     * False if all posts in the loop created with default WordPress editors.
     */
    public function isResourcesRequired()
    {
        $required = false;

        if(is_array($this->query->posts)) {
            $useEditorPostMeta = new UseEditorPostMeta();
            $typeKitIDPostMeta = new TypeKitIDPostMeta();

            foreach ($this->query->posts as $post) {
                $useEditorPostMeta->setPostId($post->ID);
                if($useEditorPostMeta->get()) {
                    $required = true;

                    if($this->isManageTypeKit()) {
                        $typeKitIDPostMeta->setPostId($post->ID);
                        $typeKitId = $typeKitIDPostMeta->get();
                        if($typeKitId) {
                            $this->addTypeKitId($typeKitId);
                        }
                    }
                }

                if($required && !$this->isManageTypeKit()) {
                    // Break while loop because if detected $required
                    // and we should not manage Type Kit fonts.
                    break;
                }
            }
        }

        return $required;
    }

    /**
     * Enqueue required styles (css files) for posts created with Setka Editor
     * on non admin site area.
     *
     * @return $this For chain calls.
     */
    public function enqueueResourcesScriptStyles()
    {
        wp_enqueue_script(Plugin::NAME . '-theme-plugins');
        wp_enqueue_style(Plugin::NAME . '-theme-resources');

        return $this;
    }

    /**
     * Modifies HTML script tag.
     *
     * @param string $tag    The `<script>` tag for the enqueued script.
     * @param string $handle The script's registered handle.
     *
     * @return string Modified $tag.
     */
    public function scriptLoaderTag($tag, $handle)
    {
        if(!in_array($handle, $this->asyncScripts, true)) {
            return $tag;
        }

        $tag = str_replace('<script ', '<script async ', $tag);

        return $tag;
    }

    /**
     * @return SetkaEditorAccount
     */
    public function getSetkaEditorAccount()
    {
        return $this->setkaEditorAccount;
    }

    /**
     * @param SetkaEditorAccount $setkaEditorAccount
     *
     * @return $this For chain calls.
     */
    public function setSetkaEditorAccount(SetkaEditorAccount $setkaEditorAccount)
    {
        $this->setkaEditorAccount = $setkaEditorAccount;
        return $this;
    }

    /**
     * @return bool
     */
    public function isGutenbergSupport()
    {
        return $this->gutenbergSupport;
    }

    /**
     * @param bool $gutenbergSupport
     * @return $this For chain calls.
     */
    public function setGutenbergSupport($gutenbergSupport)
    {
        $this->gutenbergSupport = $gutenbergSupport;
        return $this;
    }

    /**
     * @return AMP
     */
    public function getAmp()
    {
        return $this->amp;
    }

    /**
     * @param AMP $amp
     * @return $this For chain calls.
     */
    public function setAmp(AMP $amp)
    {
        $this->amp = $amp;
        return $this;
    }

    /**
     * @param \WP_Query $query
     * @return $this For chain calls.
     */
    public function setQuery(\WP_Query $query = null)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @param PageInterface $pluginSettingsPage
     */
    public function setPluginSettingsPage(PageInterface $pluginSettingsPage)
    {
        $this->pluginSettingsPage = $pluginSettingsPage;
    }

    /**
     * @return EditorGutenbergModule
     */
    public function getEditorGutenbergModule()
    {
        return $this->editorGutenbergModule;
    }

    /**
     * @param EditorGutenbergModule $editorGutenbergModule
     *
     * @return $this
     */
    public function setEditorGutenbergModule(EditorGutenbergModule $editorGutenbergModule)
    {
        $this->editorGutenbergModule = $editorGutenbergModule;
        return $this;
    }

    /**
     * @return bool
     */
    public function isManageTypeKit()
    {
        return $this->manageTypeKit;
    }

    /**
     * @param bool $manageTypeKit
     *
     * @return $this
     */
    public function setManageTypeKit($manageTypeKit)
    {
        $this->manageTypeKit = $manageTypeKit;
        return $this;
    }

    /**
     * Add single Type Kit ID.
     *
     * @param $id string Type Kit ID.
     * @return $this For chain calls.
     */
    public function addTypeKitId($id)
    {
        $this->typeKits[$id] = null;
        return $this;
    }

    /**
     * @return EditorAdapterJsSettings
     */
    public function getEditorAdapterJsSettings()
    {
        return $this->editorAdapterJsSettings;
    }

    /**
     * @param EditorAdapterJsSettings $editorAdapterJsSettings
     *
     * @return $this
     */
    public function setEditorAdapterJsSettings(EditorAdapterJsSettings $editorAdapterJsSettings)
    {
        $this->editorAdapterJsSettings = $editorAdapterJsSettings;
        return $this;
    }

    /**
     * @return NoticesStack
     */
    public function getNoticesStack()
    {
        return $this->noticesStack;
    }

    /**
     * @param NoticesStack $noticesStack
     * @return $this
     */
    public function setNoticesStack(NoticesStack $noticesStack)
    {
        $this->noticesStack = $noticesStack;
        return $this;
    }
}

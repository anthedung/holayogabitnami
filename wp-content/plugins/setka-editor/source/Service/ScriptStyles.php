<?php
namespace Setka\Editor\Service;

use Korobochkin\WPKit\ScriptsStyles\AbstractScriptsStyles;
use Korobochkin\WPKit\ScriptsStyles\ScriptsStylesInterface;
use Setka\Editor\Admin\Options;
use Setka\Editor\Plugin;
use Setka\Editor\Service\Config\TypeKit;
use Setka\Editor\Service\SetkaAccount\Account;
use Setka\Editor\Entries\Meta;

/**
 * Class ScriptStyles
 */
class ScriptStyles extends AbstractScriptsStyles implements ScriptsStylesInterface
{

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
        $option = new Options\EditorJS\Option();
        wp_register_script(
            $prefix . 'editor',
            $option->getValue(),
            array(),
            $version,
            true
        );

        // Setka Editor CSS
        $option = new Options\EditorCSS\Option();
        wp_register_style(
            $prefix . 'editor',
            $option->getValue(),
            array(),
            $version
        );

        return $this;
    }

    public function registerThemeResources()
    {
        $prefix  = Plugin::NAME . '-';
        $version = Plugin::VERSION;
        $local   = Options\Files\UseLocalFilesUtilities::useLocal();

        // Theme CSS
        if($local) {
            $option = new Options\ThemeResourceCSSLocal\ThemeResourceCSSLocalOption();
        } else {
            $option = new Options\ThemeResourceCSS\Option();
        }
        wp_register_style(
            $prefix . 'theme-resources',
            $option->getValue(),
            array(),
            $version
        );

        // Theme Plugins JS
        $option = new Options\ThemePluginsJS\Option();
        wp_register_script(
            $prefix . 'theme-plugins',
            $option->getValue(),
            array('jquery'),
            $version,
            true
        );

        return $this;
    }

    /**
     * Register Type Kit styles.
     *
     * @param array $ids List of Type Kit ids.
     *
     * @return $this For chain calls.
     */
    public function registerTypeKits(array $ids)
    {
        $prefix  = Plugin::NAME . '-';
        $version = Plugin::VERSION;

        foreach($ids as $idKey => $idValue) {
            $idKey = esc_attr($idKey);
            wp_register_script(
                $prefix . 'type-kit-' . $idKey,
                '//use.typekit.net/' . $idKey .  '.js',
                array(),
                $version,
                true
            );
        }

        return $this;
    }

    /**
     * Register Type Kit JS runner.
     *
     * @return $this For chain calls.
     */
    public function registerTypeKitRunner()
    {
        $prefix  = Plugin::NAME . '-';
        $version = Plugin::VERSION;

        wp_register_script(
            $prefix . 'type-kit-runner',
            $this->getBaseUrl() . 'assets/js/type-kit-runner/type-kit-runner.min.js',
            array(),
            $version,
            true
        );

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
        if(Account::isThemeResourcesAvailable() && self::isResourcesRequired()) {
            self::enqueueResourcesScriptStyles();

            if(is_singular()) {
                $typeKitMeta = new Meta\TypeKitIDMeta();
                $typeKitMeta->setPostId(get_the_ID());
                wp_enqueue_script(Plugin::NAME . '-type-kit-' . $typeKitMeta->getValue());
                wp_enqueue_script(Plugin::NAME . '-type-kit-runner');
            }
        }

        return $this;
    }

    /**
     * Check if some posts (page or any other custom post types) in the loop created with Setka Editor.
     *
     * Additionally registering Type Kits if post theme using this type of fonts.
     *
     * @see \Setka\Editor\Entries\Meta\UseEditorMeta
     *
     * @return bool Returns true if at least one post created with Setka Editor.
     * False if all posts in the loop created with default WordPress editors.
     */
    public function isResourcesRequired()
    {
        $required = false;
        $typeKit  = TypeKit::shouldPluginManageTypeKits();
        if($typeKit) {
            $typeKitIds = array();
        }

        if(have_posts()) {
            $useEditorMeta = new Meta\UseEditorMeta();
            $typeKitMeta   = new Meta\TypeKitIDMeta();

            while(have_posts()) {
                the_post();
                $useEditorMeta->setPostId(get_the_ID());
                if($useEditorMeta->getValue() === '1') {
                    $required = true;

                    if($typeKit) {
                        $typeKitMeta->setPostId(get_the_ID());
                        $typeKitId = $typeKitMeta->getValue();
                        if($typeKitId && !isset($typeKitIds[$typeKitId])) {
                            $typeKitIds[$typeKitId] = true;
                        }
                    } else {
                        break;
                    }
                }
            }
            unset($useEditorMeta, $typeKitMeta, $typeKitId);
            rewind_posts();

            if(!empty($typeKitIds)) {
                $this
                    ->registerTypeKits($typeKitIds)
                    ->registerTypeKitRunner();
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
}

<?php
namespace Setka\Editor\Admin\Service;

use Korobochkin\WPKit\ScriptsStyles\AbstractScriptsStyles;
use Korobochkin\WPKit\ScriptsStyles\ScriptsStylesInterface;
use Setka\Editor\Plugin;

/**
 * Class ScriptStyles
 */
class AdminScriptStyles extends AbstractScriptsStyles implements ScriptsStylesInterface
{
    /**
     * @inheritdoc
     */
    public function register()
    {
        $url     = $this->getBaseUrl();
        $version = Plugin::VERSION;
        $debug   = $this->isDev();

        // All styles in single file by now
        $file = ($debug) ? 'assets/css/admin/main-debug.css' : 'assets/css/admin/main.min.css';
        wp_register_style(
            'setka-editor-wp-admin-main',
            $url . $file,
            array(),
            $version
        );

        $file = ($debug) ? 'assets/js/admin/editor-adapter/editor-adapter.js' : 'assets/js/admin/editor-adapter/editor-adapter.min.js';
        wp_register_script(
            'setka-editor-wp-admin-editor-adapter',
            $url . $file,
            array('jquery', 'backbone', 'setka-editor-editor', 'wp-pointer'),
            $version,
            true
        );

        $file = ($debug) ? 'assets/js/admin/editor-adapter-initializer/editor-adapter-initializer.js' : 'assets/js/admin/editor-adapter-initializer/editor-adapter-initializer.min.js';
        wp_register_script(
            'setka-editor-wp-admin-editor-adapter-initializer',
            $url . $file,
            array('setka-editor-wp-admin-editor-adapter', 'uri-js'),
            $version,
            true
        );

        $file = ($debug) ? 'assets/js/admin/setting-pages/setting-pages.js' : 'assets/js/admin/setting-pages/setting-pages.min.js';
        wp_register_script(
            'setka-editor-wp-admin-setting-pages',
            $url . $file,
            array('jquery', 'backbone'),
            $version,
            true
        );

        $file  = 'assets/js/admin/setting-pages-initializer/setting-pages-initializer';
        $file .= ($debug) ? '.js' : '.min.js';

        wp_register_script(
            'setka-editor-wp-admin-setting-pages-initializer',
            $url . $file,
            array('setka-editor-wp-admin-setting-pages'),
            $version,
            true
        );

        return $this;
    }

    /**
     * Enqueue scripts & styles for all /wp-admin/ pages.
     *
     * @return $this For chain calls.
     */
    public function enqueueForAllPages()
    {
        wp_enqueue_style('setka-editor-wp-admin-main');
        return $this;
    }

    /**
     * Localize Editor Adapter.
     *
     * @return $this For chain calls.
     */
    public function localizeAdminEditorAdapter()
    {
        wp_localize_script(
            'setka-editor-wp-admin-editor-adapter',
            'setkaEditorAdapterL10n',
            array(
                'view' => array(
                    'editor' => array(
                        'tabName' => _x('Setka Editor', 'editor tab name', Plugin::NAME),
                        'switchToDefaultEditorsConfirm' => __('Are you sure that you want to switch to default WordPress editor? You will lose all the formatting and design created in Setka Editor.', Plugin::NAME),
                        'switchToSetkaEditorConfirm' => __('Post will be converted by Setka Editor. Its appearance may change. This action can’t be undone. Continue?', Plugin::NAME)
                    ),
                ),
                'names' => array(
                    'css' => Plugin::NAME,
                    '_'   => Plugin::_NAME_
                ),
                'settings' => Js\EditorAdapter\Settings::getSettings(),
                'pointers' => array(
                    'disabledEditorTabs' => array(
                        'target' => '#wp-content-editor-tools .wp-editor-tabs',
                        'options' => array(
                            'pointerClass' => 'wp-pointer setka-editor-pointer-centered-arrow',
                            'content' => sprintf(
                                '<h3>%s</h3><p>%s</p>',
                                __('Why Text and Visual tabs are blocked?', Plugin::NAME),
                                __('Posts created with Setka Editor may contain complex design elements that are not compatible with other post editors.', Plugin::NAME)
                            ),
                            'position' => array('edge' => 'top', 'align' => 'middle')
                        )
                    )
                )
            )
        );

        return $this;
    }

    /**
     * Enqueue scripts and styles for edit post page.
     *
     * @return $this For chain calls.
     */
    public function enqueueForEditPostPage()
    {
        // Editor
        wp_enqueue_script('setka-editor-editor');
        wp_enqueue_style('setka-editor-editor');
        wp_enqueue_style('setka-editor-theme-resources');

        // Editor Initializer for /wp-admin/ pages
        $this->localizeAdminEditorAdapter();
        wp_enqueue_script('setka-editor-wp-admin-editor-adapter-initializer');

        wp_enqueue_style('wp-pointer');

        return $this;
    }
}

<?php
namespace Setka\Editor\Admin\Service\Js\EditorAdapter;

use Setka\Editor\Admin\Options;
use Setka\Editor\Entries;

/**
 * Settings array for editor-adapter-initializer.js.
 *
 * @since 0.0.2
 *
 * Class Settings
 * @package Setka\Editor\Admin\Service\Js\EditorAdapter
 */
class Settings
{

    /**
     * Returns settings editor-adapter translations.settings.
     *
     * @since 0.0.2
     *
     * @return array Settings for editor-adapter translations.settings array field (cell).
     */
    public static function getSettings()
    {
        $defaults = self::getDefaults();

        $useEditorMeta = new Entries\Meta\UseEditorMeta();
        $useEditorMeta->setPostId(get_the_ID());
        if($useEditorMeta->isValid()) {
            $defaults['useSetkaEditor'] = $useEditorMeta->getValue();
        }

        $postLayoutMeta = new Entries\Meta\PostLayoutMeta();
        $postLayoutMeta->setPostId(get_the_ID());
        if($postLayoutMeta->isValid()) {
            $defaults['editorConfig']['layout'] = $postLayoutMeta->getValue();
        }

        $postThemeMeta = new Entries\Meta\PostThemeMeta();
        $postThemeMeta->setPostId(get_the_ID());
        if($postThemeMeta->isValid()) {
            $defaults['editorConfig']['theme'] = $postThemeMeta->getValue();
        }

        $typeKitIDMeta = new Entries\Meta\TypeKitIDMeta();
        $typeKitIDMeta->setPostId(get_the_ID());
        $defaults['editorConfig']['typeKitId'] = $typeKitIDMeta->getValue();

        $publicTokenOption                        = new Options\PublicToken\PublicTokenOption();
        $defaults['editorConfig']['public_token'] = $publicTokenOption->getValue();

        if(Options\Files\UseLocalFilesUtilities::useLocal()) {
            $themeJson = new Options\ThemeResourceJSLocal\ThemeResourceJSLocalOption();
        } else {
            $themeJson = new Options\ThemeResourceJS\Option();
        }
        $defaults['themeData'] = $themeJson->getValue();

        return $defaults;
    }

    /**
     * Returns default settings for editor-adapter which will be overwritten by post data.
     *
     * @since 0.0.2
     *
     * @return array Default settings.
     */
    public static function getDefaults()
    {
        $user = get_userdata(get_current_user_id());
        if(is_a($user, \WP_User::class)) {
            $caps = $user->get_role_caps();
        } else {
            $caps = array();
        }
        unset($user);

        $settings = array(
            'useSetkaEditor' => false,
            'editorConfig' => array(
                'medialib_image_alt_attr' => true,
                'user' => array(
                    'capabilities' => $caps,
                ),
                'public_token' => '',
            ),
        );
        return $settings;
    }
}

<?php
namespace Setka\Editor\Admin\Service\Js;

use Setka\Editor\PostMetas\PostLayoutPostMeta;
use Setka\Editor\PostMetas\PostThemePostMeta;
use Setka\Editor\PostMetas\TypeKitIDPostMeta;
use Setka\Editor\PostMetas\UseEditorPostMeta;
use Setka\Editor\Service\DataFactory;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;

/**
 * Settings array for editor-adapter-initializer.js.
 *
 * @since 0.0.2
 *
 * Class Settings
 * @package Setka\Editor\Admin\Service\Js\EditorAdapter
 */
class EditorAdapterJsSettings
{
    /**
     * @var DataFactory
     */
    protected $dataFactory;

    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * EditorAdapterJsSettings constructor.
     * @param DataFactory $dataFactory
     */
    public function __construct(DataFactory $dataFactory, SetkaEditorAccount $setkaEditorAccount)
    {
        $this->dataFactory        = $dataFactory;
        $this->setkaEditorAccount = $setkaEditorAccount;
    }

    /**
     * Returns settings editor-adapter translations.settings.
     *
     * @since 0.0.2
     *
     * @return array Settings for editor-adapter translations.settings array field (cell).
     */
    public function getSettings()
    {
        $defaults = $this->getDefaults();

        /**
         * @var $useEditorPostMeta UseEditorPostMeta
         */
        $useEditorPostMeta = $this->getDataFactory()->create(UseEditorPostMeta::class);
        $useEditorPostMeta->setPostId(get_the_ID());
        if($useEditorPostMeta->isValid()) {
            $defaults['useSetkaEditor'] = $useEditorPostMeta->get();
        }

        /**
         * @var $postLayoutPostMeta PostLayoutPostMeta
         */
        $postLayoutPostMeta = $this->getDataFactory()->create(PostLayoutPostMeta::class);
        $postLayoutPostMeta->setPostId(get_the_ID());
        if($postLayoutPostMeta->isValid()) {
            $defaults['editorConfig']['layout'] = $postLayoutPostMeta->get();
        }

        /**
         * @var $postThemePostMeta PostThemePostMeta
         */
        $postThemePostMeta = $this->getDataFactory()->create(PostThemePostMeta::class);
        $postThemePostMeta->setPostId(get_the_ID());
        if($postThemePostMeta->isValid()) {
            $defaults['editorConfig']['theme'] = $postThemePostMeta->get();
        }

        /**
         * @var $typeKitIDPostMeta TypeKitIDPostMeta
         */
        $typeKitIDPostMeta = $this->getDataFactory()->create(TypeKitIDPostMeta::class);
        $typeKitIDPostMeta->setPostId(get_the_ID());
        $defaults['editorConfig']['typeKitId'] = $typeKitIDPostMeta->get();

        $defaults['editorConfig']['public_token'] = $this->getSetkaEditorAccount()->getPublicTokenOption()->get();

        if($this->getSetkaEditorAccount()->isLocalFilesUsage()) {
            $themeJson = $this->getSetkaEditorAccount()->getThemeResourceJSLocalOption();
        } else {
            $themeJson = $this->getSetkaEditorAccount()->getThemeResourceJSOption();
        }
        $defaults['themeData'] = $themeJson->get();

        return $defaults;
    }

    /**
     * Returns default settings for editor-adapter which will be overwritten by post data.
     *
     * @since 0.0.2
     *
     * @return array Default settings.
     */
    public function getDefaults()
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

    /**
     * @return DataFactory
     */
    public function getDataFactory()
    {
        return $this->dataFactory;
    }

    /**
     * @param DataFactory $dataFactory
     *
     * @return $this
     */
    public function setDataFactory(DataFactory $dataFactory)
    {
        $this->dataFactory = $dataFactory;
        return $this;
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
     * @return $this
     */
    public function setSetkaEditorAccount(SetkaEditorAccount $setkaEditorAccount)
    {
        $this->setkaEditorAccount = $setkaEditorAccount;
        return $this;
    }
}

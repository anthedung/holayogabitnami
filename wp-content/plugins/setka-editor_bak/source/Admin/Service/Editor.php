<?php
namespace Setka\Editor\Admin\Service;

use Setka\Editor\Admin\Options\EditorAccessPostTypesUtilities;
use Setka\Editor\Admin\User\Capabilities;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;

/**
 * Class Editor
 */
class Editor
{
    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * @var EditorAccessPostTypesUtilities
     */
    protected $editorAccessPostTypesUtilities;

    /**
     * Editor constructor.
     * @param SetkaEditorAccount $setkaEditorAccount
     * @param EditorAccessPostTypesUtilities $editorAccessPostTypesUtilities
     */
    public function __construct(
        SetkaEditorAccount $setkaEditorAccount,
        EditorAccessPostTypesUtilities $editorAccessPostTypesUtilities
    ) {
        $this->setkaEditorAccount             = $setkaEditorAccount;
        $this->editorAccessPostTypesUtilities = $editorAccessPostTypesUtilities;
    }

    /**
     * Checks if we need add Grid Editor to page.
     *
     * @since 0.0.1
     *
     * @return bool Enable editor or not.
     */
    public function enableEditor()
    {
        if(!current_user_can(Capabilities\UseEditorCapability::NAME)) {
            return false;
        }

        if(!$this->setkaEditorAccount->isEditorResourcesAvailable()) {
            return false;
        }

        $screen = get_current_screen();
        if($this->editorAccessPostTypesUtilities->isEditorEnabledForPostType($screen->post_type)
            &&
            'post' === $screen->base
        ) {
            return true;
        }

        return false;
    }
}

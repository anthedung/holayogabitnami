<?php
namespace Setka\Editor\Admin\Service;

use Setka\Editor\Admin\User\Capabilities;
use Setka\Editor\Admin\Options;
use Setka\Editor\Service\SetkaAccount\Account;
use Setka\Editor\Entries;

class Editor
{

    /**
     * Checks if we need add Grid Editor to page.
     *
     * @since 0.0.1
     *
     * @return bool Enable editor or not.
     */
    public static function enableEditor()
    {

        if(!current_user_can(Capabilities\UseEditorCapability::NAME)) {
            return false;
        }

        if(!Account::isEditorResourcesAvailable()) {
            return false;
        }

        $current_screen = get_current_screen();
        if(Options\EditorAccessPostTypes\Utilities::isEditorEnabledForPostType($current_screen->post_type)
            &&
            'post' === $current_screen->base
        ) {
            return true;
        }

        return false;
    }


    /**
     * Checks if Setka Editor need to be automatically launched.
     *
     * @since 1.0.4
     *
     * @return bool True if Setka Editor need to be automatically launched after page loaded.
     */
    public static function isAutoInitEnabled()
    {
        if(isset($_GET['setka-editor-auto-init'])) { // WPCS: input var ok, CSRF ok.
            return true;
        }

        $use_editor_meta = new Entries\Meta\UseEditorMeta();
        $use_editor_meta->setPostId(get_the_ID());
        if($use_editor_meta->getValue() === '1') {
            return true;
        }

        return false;
    }
}

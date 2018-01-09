<?php
namespace Setka\Editor\Admin\MetaBoxes\InvitationToRegister;

use Setka\Editor\Plugin;
use Setka\Editor\Admin\Prototypes\MetaBoxes\AbstractMetaBox;
use Setka\Editor\Admin\Options\EditorAccessPostTypes;

class InvitationToRegisterMetaBox extends AbstractMetaBox
{
    public function __construct()
    {
        $postTypesOption = new EditorAccessPostTypes\Option();
        $view            = new InvitationToRegisterView();

        $this
            ->setId(Plugin::_NAME_.'_invitation_to_registerMetaBox')
            ->setTitle(_x('Setka Editor Registration', 'MetaBox title.', Plugin::NAME))
            ->setScreen($postTypesOption->getValue())
            ->setContext('side')
            ->setView($view);
    }
}

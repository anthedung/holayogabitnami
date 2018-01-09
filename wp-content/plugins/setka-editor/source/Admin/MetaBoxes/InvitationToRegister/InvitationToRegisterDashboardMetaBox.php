<?php
namespace Setka\Editor\Admin\MetaBoxes\InvitationToRegister;

use Setka\Editor\Admin\Prototypes\MetaBoxes\AbstractDashboardMetaBox;
use Setka\Editor\Plugin;

class InvitationToRegisterDashboardMetaBox extends AbstractDashboardMetaBox
{
    public function __construct() {
        $view = new InvitationToRegisterView();

        $this
            ->setId(Plugin::_NAME_ . '_invitation_to_registerDashboardMetaBox')
            ->setTitle(_x('Setka Editor Registration', 'MetaBox title.', Plugin::NAME))
            ->setView($view);
    }
}

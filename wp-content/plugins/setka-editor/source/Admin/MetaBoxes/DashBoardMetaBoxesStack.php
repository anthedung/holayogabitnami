<?php
namespace Setka\Editor\Admin\MetaBoxes;

use Korobochkin\WPKit\MetaBoxes\MetaBoxInterface;
use Korobochkin\WPKit\MetaBoxes\MetaBoxStack;
use Korobochkin\WPKit\MetaBoxes\MetaBoxStackInterface;
use Setka\Editor\Admin\MetaBoxes\InvitationToRegister\InvitationToRegisterDashboardMetaBox;
use Setka\Editor\Service\SetkaAccount\Account;

/**
 * Class DashBoardMetaBoxesStack
 */
class DashBoardMetaBoxesStack extends MetaBoxStack implements MetaBoxStackInterface
{
    /**
     * @inheritdoc
     */
    public function initialize()
    {
        if(!Account::isLoggedIn()) {
            /**
             * @var $metaBox MetaBoxInterface
             */
            $metaBox = $this->get(InvitationToRegisterDashboardMetaBox::class);
            $this->addMetaBox($metaBox);
        }

        return $this;
    }
}

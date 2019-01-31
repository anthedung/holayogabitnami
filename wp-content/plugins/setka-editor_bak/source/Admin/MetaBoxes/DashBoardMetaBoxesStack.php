<?php
namespace Setka\Editor\Admin\MetaBoxes;

use Korobochkin\WPKit\MetaBoxes\MetaBoxInterface;
use Korobochkin\WPKit\MetaBoxes\MetaBoxStack;
use Korobochkin\WPKit\MetaBoxes\MetaBoxStackInterface;
use Setka\Editor\Admin\MetaBoxes\InvitationToRegister\InvitationToRegisterDashboardMetaBox;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;

/**
 * Class DashBoardMetaBoxesStack
 */
class DashBoardMetaBoxesStack extends MetaBoxStack implements MetaBoxStackInterface
{
    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * DashBoardMetaBoxesStack constructor.
     * @param SetkaEditorAccount $setkaEditorAccount
     */
    public function __construct(SetkaEditorAccount $setkaEditorAccount)
    {
        $this->setkaEditorAccount = $setkaEditorAccount;
    }

    /**
     * @inheritdoc
     */
    public function initialize()
    {
        if(!$this->setkaEditorAccount->isLoggedIn()) {
            /**
             * @var $metaBox MetaBoxInterface
             */
            $metaBox = $this->get(InvitationToRegisterDashboardMetaBox::class);
            $this->addMetaBox($metaBox);
        }

        return $this;
    }
}

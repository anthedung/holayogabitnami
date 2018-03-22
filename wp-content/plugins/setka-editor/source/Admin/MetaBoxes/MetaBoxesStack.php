<?php
namespace Setka\Editor\Admin\MetaBoxes;

use Korobochkin\WPKit\MetaBoxes\MetaBoxStack;
use Korobochkin\WPKit\MetaBoxes\MetaBoxStackInterface;
use Setka\Editor\Admin\MetaBoxes\InvitationToRegister\InvitationToRegisterMetaBox;
use Setka\Editor\Service\SetkaAccount\Account;
use Setka\Editor\Admin\Options\EditorAccessPostTypes;

/**
 * Class MetaBoxesStack
 */
class MetaBoxesStack extends MetaBoxStack implements MetaBoxStackInterface
{
    /**
     * @inheritdoc
     */
    public function initialize()
    {
        if(!Account::isLoggedIn()) {
            $postTypesOption = new EditorAccessPostTypes\Option();
            $requiredScreen  = $postTypesOption->getValue();
            $currentScreen   = get_current_screen();

            if(in_array($currentScreen->id, $requiredScreen, true)) {
                $this->addMetaBox($this->container->get(InvitationToRegisterMetaBox::class));
            }
        }

        return $this;
    }
}

<?php
namespace Setka\Editor\Admin\MetaBoxes;

use Korobochkin\WPKit\MetaBoxes\MetaBoxStack;
use Korobochkin\WPKit\MetaBoxes\MetaBoxStackInterface;
use Setka\Editor\Admin\MetaBoxes\InvitationToRegister\InvitationToRegisterMetaBox;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;
use Setka\Editor\Admin\Options\EditorAccessPostTypesOption;

/**
 * Class MetaBoxesStack
 */
class MetaBoxesStack extends MetaBoxStack implements MetaBoxStackInterface
{
    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * MetaBoxesStack constructor.
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
            $postTypesOption = new EditorAccessPostTypesOption();
            $requiredScreen  = $postTypesOption->get();
            $currentScreen   = get_current_screen();
            if(in_array($currentScreen->id, $requiredScreen, true)) {
                $this->addMetaBox($this->get(InvitationToRegisterMetaBox::class));
            }
        }

        return $this;
    }
}

<?php
namespace Setka\Editor\Admin\Cron;

use Korobochkin\WPKit\Cron\AbstractCronSingleEvent;
use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;

/**
 * Class UserSignedUpCronEvent
 */
class UserSignedUpCronEvent extends AbstractCronSingleEvent
{
    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * @var SetkaEditorAPI\API
     */
    protected $setkaEditorAPI;

    /**
     * UserSignedUpCronEvent constructor.
     */
    public function __construct()
    {
        $this
            ->immediately()
            ->setName(Plugin::_NAME_ . '_cron_user_signed_up');
    }

    public function execute()
    {
        if(!$this->setkaEditorAccount->isLoggedIn() || !$this->setkaEditorAccount->isTokenValid()) {
            return $this;
        }

        $this->setkaEditorAPI
            ->setAuthCredits(
                new SetkaEditorAPI\AuthCredits(
                    $this->setkaEditorAccount->getTokenOption()->get()
                )
            );

        $action = new SetkaEditorAPI\Actions\UpdateStatusAction();
        $action->setRequestDetails(array(
            'body' => array(
                'status' => 'plugin_installed',
            ),
        ));

        $this->setkaEditorAPI->request($action);

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
     * @return $this
     */
    public function setSetkaEditorAccount(SetkaEditorAccount $setkaEditorAccount)
    {
        $this->setkaEditorAccount = $setkaEditorAccount;
        return $this;
    }

    /**
     * @return SetkaEditorAPI\API
     */
    public function getSetkaEditorAPI()
    {
        return $this->setkaEditorAPI;
    }

    /**
     * @param SetkaEditorAPI\API $setkaEditorAPI
     * @return $this
     */
    public function setSetkaEditorAPI(SetkaEditorAPI\API $setkaEditorAPI)
    {
        $this->setkaEditorAPI = $setkaEditorAPI;
        return $this;
    }
}

<?php
namespace Setka\Editor\Admin\Cron;

use Korobochkin\WPKit\Cron\AbstractCronSingleEvent;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;
use Setka\Editor\Service\SetkaAccount\SignIn;

/**
 * Class SyncAccountCronEvent
 */
class SyncAccountCronEvent extends AbstractCronSingleEvent
{
    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * SyncAccountCronEvent constructor.
     */
    public function __construct()
    {
        $this->setName(Plugin::_NAME_ . '_cron_sync_account');
    }

    public function execute()
    {
        if(!$this->setkaEditorAccount->isLoggedIn() || !$this->setkaEditorAccount->isTokenValid()) {
            return $this;
        }

        $this->setkaEditorAccount
            ->getSignIn()
            ->signInByToken($this->setkaEditorAccount->getTokenOption()->get(), false);

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
}

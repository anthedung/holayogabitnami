<?php
namespace Setka\Editor\Admin\Cron;

use Korobochkin\WPKit\Cron\AbstractCronEvent;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;

/**
 * Class UpdateAnonymousAccountTask
 *
 * This task get and save updates for Setka Editor if anonynous account is used (without license key).
 */
class UpdateAnonymousAccountCronEvent extends AbstractCronEvent
{
    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    public function __construct()
    {
        $this->setTimestamp(1);
        $this->setRecurrence('daily');
        $this->setName(Plugin::_NAME_.'_update_anonymous_account');
    }

    public function execute()
    {
        $this->getSetkaEditorAccount()->getSignIn()->signInAnonymous();
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

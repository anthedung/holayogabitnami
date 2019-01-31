<?php
namespace Setka\Editor\Admin\Cron;

use Korobochkin\WPKit\Cron\AbstractCronSingleEvent;
use Setka\Editor\Admin\Options;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\Account;
use Setka\Editor\Service\SetkaAccount\SignIn;

class SyncAccountCronEvent extends AbstractCronSingleEvent
{

    public function __construct()
    {
        $this->setName(Plugin::_NAME_ . '_cron_sync_account');
    }

    public function execute()
    {
        if(!Account::isLoggedIn()) {
            return;
        }

        $token = new Options\Token\Option();

        if(!$token->isValid()) {
            return;
        }

        SignIn::signInByToken($token->getValue(), false);
    }
}

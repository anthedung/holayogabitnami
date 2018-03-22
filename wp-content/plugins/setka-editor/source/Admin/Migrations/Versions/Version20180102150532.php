<?php
namespace Setka\Editor\Admin\Migrations\Versions;

use Setka\Editor\Admin\Migrations\MigrationInterface;
use Setka\Editor\Admin\Options;
use Setka\Editor\Service\SetkaAccount\Account;
use Setka\Editor\Service\SetkaAccount\SignIn;

/**
 * Class Version20180102150532
 *
 * Migration fixing duplicated setka_editor_update_anonymous_account cron tasks.
 */
class Version20180102150532 implements MigrationInterface
{
    public function up()
    {
        if(Account::isLoggedIn()) {
            $tokenOption = new Options\Token\Option();
            SignIn::signInByToken($tokenOption->getValue());
        } else {
            SignIn::signInAnonymous();
        }

        return $this;
    }
}

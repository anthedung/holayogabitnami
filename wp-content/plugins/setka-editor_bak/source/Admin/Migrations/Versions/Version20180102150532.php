<?php
namespace Setka\Editor\Admin\Migrations\Versions;

use Setka\Editor\Admin\Migrations\MigrationInterface;
use Setka\Editor\Admin\Options;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;
use Setka\Editor\Service\SetkaAccount\SignIn;

/**
 * Class Version20180102150532
 *
 * Migration fixing duplicated setka_editor_update_anonymous_account cron tasks.
 */
class Version20180102150532 implements MigrationInterface
{
    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * Version20180102150532 constructor.
     * @param SetkaEditorAccount $setkaEditorAccount
     */
    public function __construct(SetkaEditorAccount $setkaEditorAccount)
    {
        $this->setkaEditorAccount = $setkaEditorAccount;
    }

    /**
     * @inheritdoc
     */
    public function up()
    {
        if($this->setkaEditorAccount->isLoggedIn()) {
            $tokenOption = new Options\TokenOption();
            $this->setkaEditorAccount->getSignIn()->signInByToken($tokenOption->get());
        } else {
            $this->setkaEditorAccount->getSignIn()->signInAnonymous();
        }

        return $this;
    }
}

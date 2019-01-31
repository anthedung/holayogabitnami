<?php
namespace Setka\Editor\Admin\Cron;

use Korobochkin\WPKit\Cron\AbstractCronSingleEvent;
use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Options;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\Account;

class UserSignedUpCronEvent extends AbstractCronSingleEvent
{

    public function __construct()
    {
        $this->immediately();
        $this->setName(Plugin::_NAME_ . '_cron_user_signed_up');
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

        $api = SetkaAPI\APIFactory::create();
        $api->setAuthCredits(new SetkaAPI\AuthCredits($token->getValue()));
        $action = new SetkaAPI\Actions\UpdateStatusAction();
        $action->setRequestDetails(array(
            'body' => array(
                'status' => 'plugin_installed'
            )
        ));
        $api->request($action);
    }
}

<?php
namespace Setka\Editor\Admin\Cron;

use Korobochkin\WPKit\Cron\AbstractCronSingleEvent;
use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Options;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\Account;

class SetkaPostCreatedCronEvent extends AbstractCronSingleEvent
{

    public function __construct()
    {
        $this->immediately();
        $this->setName(Plugin::_NAME_ . '_cron_setka_post_created');
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
                'status' => 'post_saved'
            )
        ));
        $api->request($action);

        // Delete setting if request was unsuccessful (the action have errors).
        // We make this in order to \Setka\Editor\Admin\Service\SavePost::proceeding()
        // could try add this cron task again.
        if($action->getErrors()->hasErrors()) {
            $setka_post_created = new Options\SetkaPostCreated\Option();
            $setka_post_created->delete();
        }
    }
}

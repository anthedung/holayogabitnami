<?php
namespace Setka\Editor\Admin\Cron\Tasks\Files;

use Setka\Editor\Admin\Prototypes\Cron;
use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;
use Setka\Editor\Admin\Service\SetkaAPI\Actions\SendFilesStatAction;
use Setka\Editor\Admin\Service\SetkaAPI\APIFactory;
use Setka\Editor\Admin\Service\SetkaAPI\AuthCredits;
use Setka\Editor\Entries\PostStatuses;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\Account;

class SendFilesStatTask extends Cron\AbstractTask {

    public function __construct() {
        $this->immediately();
        $this->setOnce(true);
        $this->setHook(Plugin::_NAME_ . '_cron_files_send_files_stat');
    }

    public function execute() {
        if(!Account::is_logged_in())
            return;

        $token = new Options\Token\Option();

        $api = APIFactory::create();
        $api->setAuthCredits(new AuthCredits($token->getValue()));
        $action = new SendFilesStatAction();

        $manager = FilesManagerFactory::create();
        $stat = $manager->getFilesStat();

        $statFixed = array(
            'downloaded' => 0,
            'failed' => 0,
            'archived' => 0,
            'queued' => 0,
            'total' => 0,
        );

        $statFixed['downloaded'] = $stat[PostStatuses::PUBLISH];
        $statFixed['failed'] = $stat[PostStatuses::PENDING];
        $statFixed['archived'] = $stat[PostStatuses::ARCHIVE];
        $statFixed['queued'] = $stat[PostStatuses::DRAFT];
        $statFixed['total'] = $stat[PostStatuses::ANY];

        $statFixed['files_source'] = 'cdn';

        $useLocalFilesOption = new Options\Files\UseLocalFilesOption();
        if('1' === $useLocalFilesOption->getValue()) {
            $statFixed['files_source'] = 'self';
        }

        $action->setRequestDetails(array(
            'body' => array(
                'event' => $statFixed,
            ),
        ));

        $api->request($action);
    }
}

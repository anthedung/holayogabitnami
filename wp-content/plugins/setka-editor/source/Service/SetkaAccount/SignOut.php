<?php
namespace Setka\Editor\Service\SetkaAccount;

use Korobochkin\WPKit\Cron\CronEventInterface;
use Setka\Editor\Admin\Cron;
use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Prototypes\Options\OptionInterface;
use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;

class SignOut
{

    public static function signOutAction()
    {

        /**
         * @var $options OptionInterface[]
         */
        $options = array();

        $options[] = new Options\EditorCSS\Option();
        $options[] = new Options\EditorJS\Option();
        $options[] = new Options\EditorVersion\Option();
        $options[] = new Options\Files\FilesOption();
        $options[] = new Options\Files\FileSyncFailureOption();
        $options[] = new Options\Files\FileSyncStageOption();
        $options[] = new Options\Files\UseLocalFilesOption();
        $options[] = new Options\PlanFeatures\PlanFeaturesOption();
        $options[] = new Options\PublicToken\PublicTokenOption();
        $options[] = new Options\SetkaPostCreated\Option();

        $options[] = new Options\SubscriptionActiveUntil\Option();
        $options[] = new Options\SubscriptionPaymentStatus\Option();
        $options[] = new Options\SubscriptionStatus\Option();

        $options[] = new Options\ThemePluginsJS\Option();
        $options[] = new Options\ThemeResourceCSS\Option();
        $options[] = new Options\ThemeResourceCSSLocal\ThemeResourceCSSLocalOption();
        $options[] = new Options\ThemeResourceJS\Option();
        $options[] = new Options\ThemeResourceJSLocal\ThemeResourceJSLocalOption();

        $options[] = new Options\Token\Option();
        $options[] = new Options\WhiteLabel\WhiteLabelOption();

        foreach($options as $option) {
            $option->delete();
        }

        unset($options, $option);

        /**
         * @var $tasks CronEventInterface[]
         */
        $tasks = array();

        // Files tasks
        $tasks[] = new Cron\Files\FilesManagerCronEvent();
        $tasks[] = new Cron\Files\FilesQueueCronEvent();
        $tasks[] = new Cron\Files\SendFilesStatCronEvent();

        $tasks[] = new Cron\SetkaPostCreatedCronEvent();

        $tasks[] = new Cron\SyncAccountCronEvent();

        $tasks[] = new Cron\UserSignedUpCronEvent();
        $tasks[] = new Cron\UpdateAnonymousAccountCronEvent();

        foreach($tasks as $task) {
            $task->unScheduleAll();
        }

        unset($tasks, $task);

        try {
            $filesManager = FilesManagerFactory::create();
            $filesManager->markAllFilesAsArchived();
        } catch (\Exception $exception) {
            // Do nothing since we are deleting our plugin.
        }
    }
}

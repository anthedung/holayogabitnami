<?php
namespace Setka\Editor\Service\SetkaAccount;

use Setka\Editor\Admin\Cron\Tasks;
use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Prototypes\Cron\TaskInterface;
use Setka\Editor\Admin\Prototypes\Options\OptionInterface;
use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;

class SignOut {

	public static function sign_out() {

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
		 * @var $tasks TaskInterface[]
		 */
		$tasks = array();

		// Files tasks
		$tasks[] = new Tasks\Files\FilesManagerTask();
		$tasks[] = new Tasks\Files\FilesQueueTask();
		$tasks[] = new Tasks\Files\SendFilesStatTask();

		$tasks[] = new Tasks\SetkaPostCreated\Task();

		$tasks[] = new Tasks\SyncAccount\SyncAccountTask();

		$tasks[] = new Tasks\UserSignedUp\Task();

		foreach($tasks as $task) {
			$task->unRegisterHook();
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

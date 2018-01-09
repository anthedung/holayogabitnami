<?php
namespace Setka\Editor\Admin\Cron\Tasks\Common;

use Setka\Editor\Admin\Cron\Tasks;

class Utilities {

	/**
	 * @return \Setka\Editor\Admin\Prototypes\Cron\TaskInterface[]
	 */
	public static function get_all_cron_tasks() {
		$tasks = array(
			Tasks\Files\FilesManagerTask::class,
			Tasks\Files\FilesQueueTask::class,
			Tasks\Files\SendFilesStatTask::class,
			Tasks\SetkaPostCreated\Task::class,
			Tasks\SyncAccount\SyncAccountTask::class,
			Tasks\UpdateAnonymousAccountTask::class,
			Tasks\UserSignedUp\Task::class,
		);

		return $tasks;
	}

	/**
	 * Removes all options from DB. This is a helper method for plugin Uninstaller.
	 *
	 * @see \Setka\Editor\Service\Uninstall::run()
	 */
	public static function remove_all_cron_tasks_from_db() {
		$tasks = self::get_all_cron_tasks();

		foreach( $tasks as $task ) {
			try {
				$task_instance = new $task();
				if( is_a( $task_instance, 'Setka\Editor\Admin\Prototypes\Cron\TaskInterface' ) ) {
					/**
					 * @var $task_instance \Setka\Editor\Admin\Prototypes\Cron\TaskInterface
					 */
					$task_instance->unRegisterHook();
				}
			} catch (\Exception $exception) {
				// Do nothing.
			}
		}
	}
}

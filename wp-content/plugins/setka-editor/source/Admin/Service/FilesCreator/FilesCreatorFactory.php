<?php
namespace Setka\Editor\Admin\Service\FilesCreator;

use Setka\Editor\Admin\Options\Files\FilesOption;
use Setka\Editor\Admin\Service\ContinueExecution\CronLock;

class FilesCreatorFactory
{

    public static function createFilesCreator()
    {
        $files        = new FilesOption();
        $filesCreator = new FilesCreator($files);

        if(defined('DOING_CRON') && true === DOING_CRON) {
            $filesCreator->setContinueExecution(array(CronLock::class, 'check'));
        } else {
            $filesCreator->setContinueExecution('__return_true');
        }

        return $filesCreator;
    }
}

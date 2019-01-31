<?php
namespace Setka\Editor\Admin\Service\FilesSync;

use Monolog\Logger;
use Setka\Editor\Admin\Service\ContinueExecution\CronLock;
use Setka\Editor\Plugin;
use Setka\Editor\Service\Config\Files;
use Setka\Editor\Service\LoggerFactory;

class SynchronizerFactory
{

    /**
     * Returns Synchronizer instance.
     *
     * This factory prepare instance to usage.
     * 1. Set filesystem instance.
     * 2. Set downloader instance.
     * 3. Set path where files need to be saved.
     * 4. Set logger.
     * 5. Set ContinueExecution callback for WP CRON.
     *
     * @param null $logger
     *
     * @return Synchronizer
     * @throws \Exception
     */
    public static function create($logger = null)
    {

        $fs = FilesystemFactory::create();

        $downloader = new WordPressDownloader();

        $path = Files::getPath();

        if(!is_a($logger, Logger::class)) {
            $logger = LoggerFactory::create(Plugin::_NAME_ . '_synchronizer');
        }

        $sync = new Synchronizer($fs, $downloader, $path, $logger);

        if(defined('DOING_CRON') && true === DOING_CRON) {
            $sync->setContinueExecution(array(CronLock::class, 'check'));
        } else {
            $sync->setContinueExecution('__return_true');
        }

        return $sync;
    }
}

<?php
namespace Setka\Editor\Service;

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Setka\Editor\Plugin;
use Setka\Editor\Service\Config\PluginConfig;

class LoggerFactory
{

    /**
     * Creates the Logger instance.
     *
     * It also additional set StreamHandler if WP in debug mode. And NullHandler
     * for production site (to prevent log leak).
     *
     * @param string $name Name of chanel for logger.
     *
     * @return Logger Instance for logging.
     */
    public static function create($name = null)
    {

        if(!$name) {
            $name = Plugin::_NAME_;
        }

        $logger = apply_filters('setka_editor_logger', null, $name);

        if(is_a($logger, Logger::class)) {
            return $logger;
        }

        $logger = new Logger($name);

        if(defined('WP_DEBUG') && true === WP_DEBUG && !PluginConfig::isVIP()) {
            $file    = apply_filters('setka_editor_log_path', PathsAndUrls::getPluginDirPath('logs/main.log'), $name);
            $handler = new StreamHandler($file);
        } else {
            $handler = new NullHandler();
        }
        $logger->pushHandler($handler);

        if(defined('WP_CLI') && true === WP_CLI) {
            $consoleHandler = new StreamHandler('php://stdout');
            $logger->pushHandler($consoleHandler);
        }

        return $logger;
    }
}

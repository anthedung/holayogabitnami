<?php
namespace Setka\Editor\Admin\Pages;

use Korobochkin\WPKit\Runners\RunnerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class QuickLinksRunner
 */
class QuickLinksRunner implements RunnerInterface
{
    /**
     * @var ContainerInterface Container with services.
     */
    protected static $container;

    /**
     * Returns the ContainerBuilder with services.
     *
     * @return ContainerInterface Container with services.
     */
    public static function getContainer()
    {
        return self::$container;
    }

    /**
     * Sets the ContainerBuilder with services.
     *
     * @param ContainerInterface $container Container with services.
     */
    public static function setContainer(ContainerInterface $container = null)
    {
        self::$container = $container;
    }

    /**
     * @inheritdoc
     */
    public static function run()
    {
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        /**
         * @var $quickLinks QuickLinks
         */
        $quickLinks = self::getContainer()->get(QuickLinks::class);

        if($quickLinks->isAllowed()) {
            $quickLinks->addFilters();
        }
    }
}

<?php
namespace Setka\Editor\API\V1;

use Korobochkin\WPKit\AlmostControllers\HttpStack;
use Korobochkin\WPKit\Runners\RunnerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SetkaEditorPluginHttpStackRunner
 */
class SetkaEditorPluginHttpStackRunner implements RunnerInterface
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
        /**
         * @var $http HttpStack
         */
        $http = self::getContainer()->get('wp.plugins.setka_editor.web_hooks');
        $http->register();
    }
}

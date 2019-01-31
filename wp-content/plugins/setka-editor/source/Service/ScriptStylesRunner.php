<?php
namespace Setka\Editor\Service;

use Korobochkin\WPKit\Runners\RunnerInterface;
use Setka\Editor\Plugin;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ScriptStylesRunner
 */
class ScriptStylesRunner implements RunnerInterface
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
    }

    /**
     * Register main resources.
     */
    public static function register()
    {
        self::getContainer()->get(ScriptStyles::class)->register();
    }

    /**
     * Register theme resources.
     *
     * @see Plugin::run()
     */
    public static function registerThemeResources()
    {
        self::getContainer()->get(ScriptStyles::class)->registerThemeResources();
    }

    /**
     * Enqueue CSS and JS.
     *
     * @see Plugin::run()
     */
    public static function enqueue()
    {
        self::getContainer()->get(ScriptStyles::class)->enqueue();
    }
}

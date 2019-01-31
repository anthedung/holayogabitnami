<?php
namespace Setka\Editor\Service;

use Korobochkin\WPKit\Runners\RunnerInterface;
use Setka\Editor\Plugin;
use Setka\Editor\Service\Config\PluginConfig;
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
        /**
         * @var $scriptStyles ScriptStyles
         */
        global $wp_query;
        $scriptStyles = self::getContainer()->get(ScriptStyles::class);
        $scriptStyles->setQuery($wp_query)->enqueue();
    }

    /**
     * Modifies HTML script tag.
     *
     * @param string $tag    The `<script>` tag for the enqueued script.
     * @param string $handle The script's registered handle.
     *
     * @return string Modified $tag.
     */
    public static function scriptLoaderTag($tag, $handle)
    {
        return self::getContainer()->get(ScriptStyles::class)->scriptLoaderTag($tag, $handle);
    }

    /**
     * Output Type Kit Fonts.
     */
    public static function footer()
    {
        self::getContainer()->get(ScriptStyles::class)->footer();
    }

    /**
     * Register Gutenberg blocks.
     */
    public static function registerGutenberg()
    {
        self::getContainer()->get(ScriptStyles::class)->registerGutenberg();
    }
}

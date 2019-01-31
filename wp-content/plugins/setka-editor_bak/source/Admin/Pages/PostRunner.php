<?php
namespace Setka\Editor\Admin\Pages;

use Korobochkin\WPKit\Runners\RunnerInterface;
use Setka\Editor\Admin\Service\AdminScriptStyles;
use Setka\Editor\Service\Config\PluginConfig;
use Setka\Editor\Admin\Service\Editor;
use Setka\Editor\Service\ScriptStyles;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PostRunner
 */
class PostRunner implements RunnerInterface
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
        add_action('admin_enqueue_scripts', array(self::class, 'adminEnqueueScripts'), 1100);
    }

    /**
     * Enqueue editor scripts & styles + initializer.
     *
     * @since 0.0.2
     */
    public static function adminEnqueueScripts()
    {
        if(self::$container->getParameter('wp.plugins.setka_editor.gutenberg_support')) {
            /**
             * @var $scriptStyles ScriptStyles
             */
            $scriptStyles = self::getContainer()->get(ScriptStyles::class);
            $scriptStyles->localizeGutenbergBlocks()->enqueueForGutenberg();
        } else {
            if(self::getContainer()->get(Editor::class)->enableEditor()) {
                /**
                 * @var $adminScriptStyles AdminScriptStyles
                 */
                $adminScriptStyles = self::getContainer()->get(AdminScriptStyles::class);
                $adminScriptStyles->enqueueForEditPostPage();
            }
        }
    }
}

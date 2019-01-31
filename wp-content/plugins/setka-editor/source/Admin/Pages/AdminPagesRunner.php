<?php
namespace Setka\Editor\Admin\Pages;

use Korobochkin\WPKit\Runners\RunnerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AdminPagesRunner implements RunnerInterface
{
    /**
     * @var ContainerInterface Container with services.
     */
    protected static $container;

    /**
     * @inheritdoc
     */
    public static function getContainer()
    {
        return self::$container;
    }

    /**
     * @inheritdoc
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
        Loader::$symfony_twig         = self::getContainer()->get('wp.plugins.setka_editor.twig');
        Loader::$symfony_form_factory = self::getContainer()->get('wp.plugins.setka_editor.form_factory');

        $pages = self::getContainer()->get(AdminPages::class);
        $pages->register();
    }
}

<?php
namespace Setka\Editor\Admin\Pages;

use Korobochkin\WPKit\Pages\PageInterface;
use Korobochkin\WPKit\Pages\Tabs\TabsInterface;
use Setka\Editor\Admin\Options\AMP\AMPCssOption;
use Setka\Editor\Admin\Options\AMP\AMPFontsOption;
use Setka\Editor\Admin\Pages\AMP\AMPPage;
use Setka\Editor\Admin\Pages\Settings\SettingsPage;
use Setka\Editor\Admin\Pages\Uninstall\UninstallPage;
use Setka\Editor\Service\DataFactory;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PluginPagesFactory
 */
class PluginPagesFactory
{
    /**
     * Create page with tabs.
     *
     * @param $className string class name implemented PageInterface.
     * @param $container ContainerInterface
     *
     * @return PageInterface Page with tabs.
     */
    public static function create($className, $container)
    {
        /**
         * @var $page PageInterface
         */
        $page = new $className();

        if ($container->get(SetkaEditorAccount::class)->isLoggedIn()) {
            $tabs = $container->get('wp.plugins.setka_editor.admin.account_tabs');
        } else {
            $tabs = $container->get('wp.plugins.setka_editor.admin.sign_up_tabs');
        }

        /**
         * @var $tabs TabsInterface
         */
        $page->setTabs($tabs);

        if(is_a($page, UninstallPage::class)) {
            $page
                ->setSetkaEditorAccount($container->get(SetkaEditorAccount::class));
        }

        if(is_a($page, SettingsPage::class)) {
            /**
             * @var $page SettingsPage
             */
            $page
                ->setNoticesStack($container->get('wp.plugins.setka_editor.notices_stack'))
                ->setDataFactory($container->get(DataFactory::class))
                ->setFormFactory($container->get('wp.plugins.setka_editor.form_factory'));
        } elseif (is_a($page, AMPPage::class)) {
            /**
             * @var $page AMPPage
             */
            $page
                ->setAMPCssOption($container->get(AMPCssOption::class))
                ->setAMPFontsOption($container->get(AMPFontsOption::class))
                ->setFormFactory($container->get('wp.plugins.setka_editor.form_factory'));
        }

        return $page;
    }
}

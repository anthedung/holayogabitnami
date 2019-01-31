<?php
namespace Setka\Editor\Admin\Pages;

use Korobochkin\WPKit\Pages\PageInterface;
use Korobochkin\WPKit\Pages\Tabs\TabsInterface;
use Setka\Editor\Admin\Notices\NoticesStack;
use Setka\Editor\Admin\Pages\Settings\SettingsPage;
use Setka\Editor\Service\SetkaAccount\Account;
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

        if (Account::isLoggedIn()) {
            $tabs = $container->get('wp.plugins.setka_editor.admin.account_tabs');
        } else {
            $tabs = $container->get('wp.plugins.setka_editor.admin.sign_up_tabs');
        }

        /**
         * @var $tabs TabsInterface
         */
        $page->setTabs($tabs);

        if(is_a($page, SettingsPage::class)) {
            /**
             * @var $page SettingsPage
             * @var $noticesStack NoticesStack
             */
            $noticesStack = $container->get('wp.plugins.setka_editor.notices_stack');
            $page->setNoticesStack($noticesStack);
        }

        return $page;
    }
}

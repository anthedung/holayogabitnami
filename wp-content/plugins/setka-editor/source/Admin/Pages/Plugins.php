<?php
namespace Setka\Editor\Admin\Pages;

use Korobochkin\WPKit\Pages\PageInterface;
use Setka\Editor\Plugin;
use Setka\Editor\Admin\Options;
use Setka\Editor\Service\Config\PluginConfig;
use Setka\Editor\Service\SetkaAccount\Account;

class Plugins
{
    /**
     * @var PageInterface
     */
    protected $pluginSettingsPage;

    /**
     * Plugins constructor.
     * @param PageInterface $pluginSettingsPage
     */
    public function __construct(PageInterface $pluginSettingsPage)
    {
        $this->pluginSettingsPage = $pluginSettingsPage;
    }

    /**
     * Adds plugin action links (along with Deactivate | Edit | Delete).
     *
     * @param $links array Default links setted up by WordPress.
     *
     * @return array Default links + our custom links.
     */
    public function addActionLinks(array $links)
    {
        if(Account::isLoggedIn()) {
            $additional = array(
                'settings' => sprintf(
                    '<a href="%1$s">%2$s</a>',
                    esc_url($this->getPluginSettingsPage()->getURL()),
                    esc_html__('Settings', Plugin::NAME)
                ),
                'upgrade' => sprintf(
                    '<a href="%1$s" target="_blank">%2$s</a>',
                    esc_url(PluginConfig::getUpgradeUrl()),
                    esc_html_x('Upgrade plan', 'Label for plugin action links (on WP plugins page)', Plugin::NAME)
                ),
            );

            $links = $additional + $links;
        }
        else {
            $additional = array(
                'start' => sprintf(
                    '<a href="%1$s">%2$s</a>',
                    esc_url($this->getPluginSettingsPage()->getURL()),
                    esc_html__('Start', Plugin::NAME)
                ),
            );

            $links = $additional + $links;
        }

        return $links;
    }

    /**
     * @return PageInterface
     */
    public function getPluginSettingsPage()
    {
        return $this->pluginSettingsPage;
    }

    /**
     * @param PageInterface $pluginSettingsPage
     */
    public function setPluginSettingsPage(PageInterface $pluginSettingsPage)
    {
        $this->pluginSettingsPage = $pluginSettingsPage;
    }
}

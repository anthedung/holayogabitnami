<?php

namespace Setka\Editor\Admin\Notices;

use Setka\Editor\Admin\Prototypes\Notices\ErrorNotice;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\Account;

class SubscriptionBlockedNotice extends ErrorNotice
{

    public function __construct()
    {
        parent::__construct(Plugin::NAME, 'subscription_blocked');
    }

    public function lateConstruct()
    {
        parent::lateConstruct();
        $content = sprintf(
            /* translators: %1$s - url to support page. */
            __('Setka Editor plugin was deactivated because of the technical error. Please contact <a href="%1$s" target="_blank">Support team</a>.', Plugin::NAME),
            'https://editor.setka.io/support'
        );
        $this->setContent('<p>' . $content . '</p>');
    }

    public function isRelevant()
    {
        if(!parent::isRelevant()) {
            return false;
        }

        if(!current_user_can('manage_options')) {
            return false;
        }

        if(!Account::isLoggedIn()) {
            return false;
        }

        if(!Account::isSubscriptionStatusRunning()) {
            return true;
        }

        return false;
    }
}

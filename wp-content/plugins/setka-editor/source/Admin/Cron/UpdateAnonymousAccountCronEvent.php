<?php
namespace Setka\Editor\Admin\Cron;

use Korobochkin\WPKit\Cron\AbstractCronEvent;
use Setka\Editor\Admin\Options;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\SignIn;

/**
 * Class UpdateAnonymousAccountTask
 *
 * This task get and save updates for Setka Editor if anonynous account is used (without license key).
 */
class UpdateAnonymousAccountCronEvent extends AbstractCronEvent
{
    public function __construct()
    {
        $this->setTimestamp(1);
        $this->setRecurrence('daily');
        $this->setName(Plugin::_NAME_.'_update_anonymous_account');
    }

    public function execute()
    {
        SignIn::signInAnonymous();
    }
}

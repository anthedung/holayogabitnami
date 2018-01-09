<?php
namespace Setka\Editor\Admin\Cron\Tasks;

use Setka\Editor\Admin\Prototypes\Cron;
use Setka\Editor\Admin\Options;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\SignIn;

/**
 * Class UpdateAnonymousAccountTask
 *
 * This task get and save updates for Setka Editor if anonynous account is used (without license key).
 */
class UpdateAnonymousAccountTask extends Cron\AbstractTask
{
    public function __construct()
    {
        $this->setTimestamp(1);
        $this->setOnce(false);
        $this->setRecurrence('daily');
        $this->setHook(Plugin::_NAME_.'_update_anonymous_account');
    }

    public function execute()
    {
        SignIn::signInAnonymous();
    }
}

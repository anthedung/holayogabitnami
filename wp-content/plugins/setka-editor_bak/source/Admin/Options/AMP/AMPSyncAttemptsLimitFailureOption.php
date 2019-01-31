<?php
namespace Setka\Editor\Admin\Options\AMP;

use Korobochkin\WPKit\Options\Special\BoolOption;
use Setka\Editor\Plugin;

class AMPSyncAttemptsLimitFailureOption extends BoolOption
{
    public function __construct()
    {
        parent::__construct();
        $this
            ->setName(Plugin::_NAME_ . '_amp_sync_attempts_limit_failure')
            ->setDefaultValue(false);
    }
}

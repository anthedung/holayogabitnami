<?php
namespace Setka\Editor\Admin\Options\AMP;

use Korobochkin\WPKit\Options\Special\BoolOption;
use Setka\Editor\Plugin;

class AMPSyncFailureOption extends BoolOption
{
    public function __construct()
    {
        parent::__construct();
        $this
            ->setName(Plugin::_NAME_ . '_amp_sync_failure')
            ->setDefaultValue(false);
    }
}

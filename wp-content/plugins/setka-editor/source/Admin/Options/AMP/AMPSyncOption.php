<?php
namespace Setka\Editor\Admin\Options\AMP;

use Korobochkin\WPKit\Options\Special\BoolOption;
use Setka\Editor\Plugin;

class AMPSyncOption extends BoolOption
{
    public function __construct()
    {
        parent::__construct();
        $this
            ->setName(Plugin::_NAME_ . '_amp_sync')
            ->setDefaultValue(true);
    }
}

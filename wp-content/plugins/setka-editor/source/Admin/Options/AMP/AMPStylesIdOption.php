<?php
namespace Setka\Editor\Admin\Options\AMP;

use Korobochkin\WPKit\Options\Special\NumericOption;
use Setka\Editor\Plugin;

class AMPStylesIdOption extends NumericOption
{
    public function __construct()
    {
        parent::__construct();
        $this
            ->setName(Plugin::_NAME_ . '_amp_styles_id')
            ->setAutoload(true)
            ->setDefaultValue(0);
    }
}

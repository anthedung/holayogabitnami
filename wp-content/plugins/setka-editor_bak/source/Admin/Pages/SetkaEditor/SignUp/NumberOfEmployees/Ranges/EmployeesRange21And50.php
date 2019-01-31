<?php
namespace Setka\Editor\Admin\Pages\SetkaEditor\SignUp\NumberOfEmployees\Ranges;

use Setka\Editor\Admin\Pages\SetkaEditor\SignUp\NumberOfEmployees\EmployeesRange;
use Setka\Editor\Plugin;

class EmployeesRange21And50 extends EmployeesRange
{

    public function __construct()
    {
        $this->setTitle(__('21−50 employees', Plugin::NAME));
        $this->setValue('21−50 employees');
    }
}

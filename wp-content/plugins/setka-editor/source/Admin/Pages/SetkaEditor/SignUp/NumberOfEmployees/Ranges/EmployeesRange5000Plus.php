<?php
namespace Setka\Editor\Admin\Pages\SetkaEditor\SignUp\NumberOfEmployees\Ranges;

use Setka\Editor\Admin\Pages\SetkaEditor\SignUp\NumberOfEmployees\EmployeesRange;
use Setka\Editor\Plugin;

class EmployeesRange5000Plus extends EmployeesRange
{

    public function __construct()
    {
        $this->setTitle(__('5,000+ employees', Plugin::NAME));
        $this->setValue('5,000+ employees');
    }
}

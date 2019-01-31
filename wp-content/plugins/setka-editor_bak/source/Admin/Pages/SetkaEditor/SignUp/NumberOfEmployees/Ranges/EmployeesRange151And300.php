<?php
namespace Setka\Editor\Admin\Pages\SetkaEditor\SignUp\NumberOfEmployees\Ranges;

use Setka\Editor\Admin\Pages\SetkaEditor\SignUp\NumberOfEmployees\EmployeesRange;
use Setka\Editor\Plugin;

class EmployeesRange151And300 extends EmployeesRange
{

    public function __construct()
    {
        $this->setTitle(__('151–300 employees', Plugin::NAME));
        $this->setValue('151–300 employees');
    }
}

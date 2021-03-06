<?php
namespace Setka\Editor\Admin\Pages\SetkaEditor\SignUp\NumberOfEmployees\Ranges;

use Setka\Editor\Admin\Pages\SetkaEditor\SignUp\NumberOfEmployees\EmployeesRange;
use Setka\Editor\Plugin;

class EmployeesRange1001And5000 extends EmployeesRange
{

    public function __construct()
    {
        $this->setTitle(__('1,001–5,000 employees', Plugin::NAME));
        $this->setValue('1,001–5,000 employees');
    }
}

<?php
namespace Setka\Editor\Admin\Pages\SetkaEditor\SignUp\NumberOfEmployees\Ranges;

use Setka\Editor\Admin\Pages\SetkaEditor\SignUp\NumberOfEmployees\EmployeesRange;
use Setka\Editor\Plugin;

class EmployeesRange51And150 extends EmployeesRange
{

    public function __construct()
    {
        $this->setTitle(__('51–150 employees', Plugin::NAME));
        $this->setValue('51–150 employees');
    }
}

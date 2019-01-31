<?php
namespace Setka\Editor\Admin\Pages\SetkaEditor\SignUp\NumberOfEmployees\Ranges;

use Setka\Editor\Admin\Pages\SetkaEditor\SignUp\NumberOfEmployees\EmployeesRange;
use Setka\Editor\Plugin;

class EmployeesRange301And1000 extends EmployeesRange
{

    public function __construct()
    {
        $this->setTitle(__('301–1,000 employees', Plugin::NAME));
        $this->setValue('301–1,000 employees');
    }
}

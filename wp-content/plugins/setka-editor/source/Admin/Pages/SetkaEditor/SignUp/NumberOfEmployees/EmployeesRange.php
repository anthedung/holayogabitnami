<?php
namespace Setka\Editor\Admin\Pages\SetkaEditor\SignUp\NumberOfEmployees;

class EmployeesRange implements EmployeesRangeInterface
{

    /**
     * @var string A text representation of range showed to users.
     */
    protected $title;

    /**
     * @var string Used in html name attrs.
     */
    protected $value;

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}

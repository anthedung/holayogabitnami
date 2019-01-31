<?php
namespace Setka\Editor\Admin\Prototypes\Notices;

class Attributes
{
    /**
     * @var array Attributes.
     */
    private $attributes = array();

    /**
     * @var array CSS classes.
     */
    private $classes = array();

    public function setAttribute($key, $value)
    {
        if ('class' === $key && !is_array($value)) {
            if (!is_array($value)) {
                $value = explode(' ', $value);
            }
            $this->setClasses($value);
            return;
        }
        if (!is_scalar($value)) {
            return;
        }
        $this->attributes[$key] = $value;
    }

    public function getAttribute($key)
    {
        if ('class' === $key) {
            $value = implode(' ', $this->getClasses());
            return $value;
        }
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
        return '';
    }

    public function getAllAttributes()
    {
        $attributes = $this->attributes;
        $classes    = $this->getClasses();
        if (!empty($classes)) {
            $attributes['class'] = implode(' ', $classes);
        }
        return $attributes;
    }

    public function addClass($class)
    {
        if (!is_scalar($class)) {
            return false;
        }
        $this->classes[$class] = true;
    }

    public function removeClass($class)
    {
        if (!is_scalar($class)) {
            return false;
        }
        if (isset($this->classes[$class])) {
            unset($this->classes[$class]);
        }
    }

    public function setClasses(array $classes)
    {
        $this->classes = array(); // clear previous values
        foreach ($classes as $class) {
            $this->addClass($class);
        }
    }

    public function getClasses()
    {
        return array_keys($this->classes);
    }
}

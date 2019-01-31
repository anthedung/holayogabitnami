<?php
namespace Setka\Editor\Admin\Prototypes\Options\Traits\Aggregate;

trait ValueTrait
{

    public function getValue()
    {

        /**
         * @var $this \Setka\Editor\Admin\Prototypes\Options\NodeInterface
         */
        if(isset($this->value)) {
            return $this->value;
        }

        $raw    = $this->getValueRaw();
        $parent = $this->getParent();

        if($parent && !is_null($raw)) {
            return $raw;
        } else {
            if($raw) {
                return $raw;
            }
        }

        return $this->getDefaultValue();
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
}

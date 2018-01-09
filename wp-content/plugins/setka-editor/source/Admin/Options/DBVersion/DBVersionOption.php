<?php
namespace Setka\Editor\Admin\Options\DBVersion;

use Setka\Editor\Admin\Prototypes\Options\AbstractOption;
use Setka\Editor\Plugin;
use Symfony\Component\Validator\Constraints;

class DBVersionOption extends AbstractOption {

    public function __construct() {
        parent::__construct(Plugin::_NAME_ . '_db_version', '');
        $this->setDefaultValue(0);
    }

    public function buildConstraint() {
        return array(
            new Constraints\Type(array(
                'type' => 'numeric',
            )),
        );
    }

    public function sanitize($instance) {
        return (int)$instance;
    }

    public function getValue() {
        return (int)parent::getValue();
    }
}

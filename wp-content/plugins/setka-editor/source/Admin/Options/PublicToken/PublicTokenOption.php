<?php
namespace Setka\Editor\Admin\Options\PublicToken;

use Setka\Editor\Admin\Prototypes\Options\AbstractOption;
use Setka\Editor\Plugin;
use Symfony\Component\Validator\Constraints;

class PublicTokenOption extends AbstractOption {

    public function __construct() {
        parent::__construct(Plugin::_NAME_ . '_public_token', Plugin::_NAME_ . '_auth');
        $this->setDefaultValue('');
    }

    public function buildConstraint() {
        return array(
            new Constraints\NotBlank(),
            new Constraints\Length(array(
                'min' => 32,
                'max' => 32,
            )),
        );
    }

    public function sanitize($instance) {
        return sanitize_text_field($instance);
    }
}

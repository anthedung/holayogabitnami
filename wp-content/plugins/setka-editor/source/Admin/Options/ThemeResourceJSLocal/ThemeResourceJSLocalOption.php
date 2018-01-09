<?php
namespace Setka\Editor\Admin\Options\ThemeResourceJSLocal;

use Setka\Editor\Admin\Prototypes;
use Setka\Editor\Plugin;
use Symfony\Component\Validator\Constraints;

class ThemeResourceJSLocalOption extends Prototypes\Options\AbstractOption {

	public function __construct() {
		parent::__construct(Plugin::_NAME_ . '_theme_resource_js_local', '');
		$this->setDefaultValue('');
	}

	public function buildConstraint() {
		return array(
			new Constraints\NotBlank(),
			new Constraints\Type('string'),
		);
	}

	public function sanitize($instance) {
		return sanitize_text_field($instance);
	}
}

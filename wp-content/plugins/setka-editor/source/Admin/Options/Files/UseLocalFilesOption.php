<?php
namespace Setka\Editor\Admin\Options\Files;

use Setka\Editor\Admin\Prototypes;
use Setka\Editor\Plugin;
use Symfony\Component\Validator\Constraints;

/**
 * Indicate should we enqueue local files or not.
 */
class UseLocalFilesOption extends Prototypes\Options\AbstractOption {

	public function __construct() {
		parent::__construct(Plugin::_NAME_ . '_use_local_files', '');
		$this->setDefaultValue('0');
	}

	public function buildConstraint() {
		return array(
			new Constraints\NotNull(),
			new Constraints\Type(array(
				'type' => 'string',
			)),
			new Constraints\Choice(array(
				'choices' => array('0', '1'),
				'multiple' => false,
				'strict' => true,
			)),
		);
	}

	public function sanitize($instance) {
		if($this->validateValue($instance)) {
			return $instance;
		}
		return $this->getDefaultValue();
	}
}

<?php
namespace Setka\Editor\Admin\Options\Files;

use Setka\Editor\Admin\Prototypes;
use Setka\Editor\Plugin;
use Symfony\Component\Validator\Constraints;

/**
 * Should we sync files (and use local files) or not?
 *
 * Another way to disable this functionality is to define constant. See more in FilesManager.
 */
class FileSyncOption extends Prototypes\Options\AbstractOption {

	public function __construct() {
		parent::__construct(Plugin::_NAME_ . '_file_sync', '');
		$this->setDefaultValue('1');
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

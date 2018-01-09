<?php
namespace Setka\Editor\Admin\Options\Files;

use Setka\Editor\Admin\Prototypes\Options\AbstractOption;
use Setka\Editor\Plugin;
use Symfony\Component\Validator\Constraints;

/**
 * List of files as array which need to be crated in DB.
 */
class FilesOption extends AbstractOption {

	public function __construct() {
		parent::__construct(Plugin::_NAME_ . '_files', '');
		$this->setDefaultValue(array());
		$this->setAutoload(false);
	}

	public function buildConstraint() {
		return array(
			new Constraints\Type(array(
				'type' => 'array',
			)),
		);
	}

	public function sanitize($instance) {
		return $instance;
	}
}

<?php
namespace Setka\Editor\Entries\Meta;

use Setka\Editor\Entries\SetkaEditorFilePostType;
use Setka\Editor\Plugin;
use Setka\Editor\Prototypes\PostMetas;
use Symfony\Component\Validator\Constraints;

class AttemptsToDownloadMeta extends PostMetas\AbstractMeta {

	public function __construct() {
		$this->setName(Plugin::_NAME_ . '_attempts_to_download');
		$this->setVisible(false);
		$this->setDefaultValue('0');
		$this->setAllowedPostTyes(array(SetkaEditorFilePostType::NAME));
	}

	public function buildConstraint() {
		return array(
			new Constraints\NotBlank(),
			new Constraints\Type('numeric'),
		);
	}

	public function sanitize($value) {
		$this->setValue($value);
		if($this->isValid()) {
			return $value;
		}
		return $this->getDefaultValue();
	}
}

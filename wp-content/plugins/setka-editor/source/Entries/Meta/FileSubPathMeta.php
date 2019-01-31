<?php
namespace Setka\Editor\Entries\Meta;

use Setka\Editor\Entries\SetkaEditorFilePostType;
use Setka\Editor\Plugin;
use Setka\Editor\Prototypes\PostMetas;
use Symfony\Component\Validator\Constraints;

class FileSubPathMeta extends PostMetas\AbstractMeta
{

    public function __construct()
    {
        $this->setName(Plugin::_NAME_ . '_file_sub_path');
        $this->setVisible(false);
        $this->setDefaultValue('');
        $this->setAllowedPostTyes(array(SetkaEditorFilePostType::NAME));
    }

    public function buildConstraint()
    {
        return array(
            new Constraints\NotBlank(),
            new Constraints\Type('string'),
        );
    }

    public function sanitize($value)
    {
        $value = sanitize_text_field($value);
        $this->setValue($value);
        if($this->isValid()) {
            return $value;
        }
        return $this->getDefaultValue();
    }
}

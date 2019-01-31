<?php
namespace Setka\Editor\Entries\Meta;

use Setka\Editor\Entries\SetkaEditorFilePostType;
use Setka\Editor\Plugin;
use Setka\Editor\Prototypes\PostMetas;
use Symfony\Component\Validator\Constraints;

class OriginUrlMeta extends PostMetas\AbstractMeta
{

    public function __construct()
    {
        $this->setName(Plugin::_NAME_ . '_origin_url');
        $this->setVisible(false);
        $this->setDefaultValue('');
        $this->setAllowedPostTyes(array(SetkaEditorFilePostType::NAME));
    }

    public function buildConstraint()
    {
        return array(
            new Constraints\NotBlank(),
            new Constraints\Url(),
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

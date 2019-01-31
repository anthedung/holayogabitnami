<?php
namespace Setka\Editor\Admin\Options;

use Korobochkin\WPKit\Options\OptionInterface;

/**
 * Class EditorAccessPostTypesUtilities
 */
class EditorAccessPostTypesUtilities
{
    /**
     * @var OptionInterface
     */
    protected $editorAccessPostTypesOption;

    /**
     * EditorAccessPostTypesUtilities constructor.
     * @param OptionInterface $editorAccessPostTypesOption
     */
    public function __construct(OptionInterface $editorAccessPostTypesOption)
    {
        $this->editorAccessPostTypesOption = $editorAccessPostTypesOption;
    }

    /**
     * @param $postType string Post type to check.
     * @return bool True if enabled.
     */
    public function isEditorEnabledForPostType($postType)
    {
        if(is_string($postType) && !empty($postType)) {
            try {
                return in_array(
                    $postType,
                    $this->editorAccessPostTypesOption->get(),
                    true
                );
            }
            catch(\Exception $exception) {
                return false;
            }
        }
        return false;
    }

    /**
     * @return OptionInterface
     */
    public function getEditorAccessPostTypesOption()
    {
        return $this->editorAccessPostTypesOption;
    }

    /**
     * @param OptionInterface $editorAccessPostTypesOption
     *
     * @return $this
     */
    public function setEditorAccessPostTypesOption(OptionInterface $editorAccessPostTypesOption)
    {
        $this->editorAccessPostTypesOption = $editorAccessPostTypesOption;
        return $this;
    }
}

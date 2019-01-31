<?php
namespace Setka\Editor\Admin\Options;

use Korobochkin\WPKit\Options\AbstractOption;
use Setka\Editor\Plugin;
use Symfony\Component\Validator\Constraints;

/**
 * Class EditorAccessPostTypesOption
 */
class EditorAccessPostTypesOption extends AbstractOption
{
    public function __construct()
    {
        $this
            ->setName(Plugin::_NAME_ . '_editor_access_post_types')
            ->setDefaultValue(array('post', 'page'));
    }

    /**
     * @inheritdoc
     */
    public function buildConstraint()
    {
        $postTypes = get_post_types();
        unset($postTypes['attachment']);
        unset($postTypes['revision']);
        unset($postTypes['nav_menu_item']);
        $postTypes = array_values($postTypes);

        return array(
            new Constraints\NotNull(),
            new Constraints\Choice(array(
                'choices' => $postTypes,
                'multiple' => true,
                'strict' => true,
            )),
        );
    }
}

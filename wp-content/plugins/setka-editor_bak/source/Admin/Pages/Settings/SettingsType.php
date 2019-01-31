<?php
namespace Setka\Editor\Admin\Pages\Settings;

use Setka\Editor\Plugin;
use Setka\Editor\Service\Constraints\WordPressNonceConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints;
use Setka\Editor\Admin\User\Capabilities;

class SettingsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $postTypes = get_post_types();
        unset($postTypes['attachment']);
        unset($postTypes['revision']);
        unset($postTypes['nav_menu_item']);

        if(!empty($postTypes)) {
            foreach($postTypes as $key => $value) {
                $postTypeObject  = get_post_type_object($value);
                $postTypes[$key] = $postTypeObject->labels->name;
            }
            $postTypes = array_flip($postTypes);

            $builder->add('post_types', Type\ChoiceType::class, array(
                'choices' => $postTypes,
                'multiple' => true,
                'expanded' => true,
            ));
        }
        unset($postTypes, $key, $value, $postTypeObject);

        $roles = get_editable_roles();
        if(!empty($roles)) {
            $rolesVariants = array();
            $rolesSelected = array();
            foreach($roles as $key => $value) {
                $rolesVariants[$value['name']] = $key;

                if(isset($value['capabilities'][Capabilities\UseEditorCapability::NAME])
                    &&
                    true === $value['capabilities'][Capabilities\UseEditorCapability::NAME]
                ) {
                    $rolesSelected[] = $key;
                }
            }

            $builder->add('roles', Type\ChoiceType::class, array(
                'choices' => $rolesVariants,
                'multiple' => true,
                'expanded' => true,
                'data' => $rolesSelected
            ));
        }
        unset($roles, $rolesVariants, $rolesSelected, $key, $value);

        $builder->add('white_label', Type\CheckboxType::class, array(
            'label' => __('Credits', Plugin::NAME),
            'required' => false,
        ));

        $builder->add('nonce', Type\HiddenType::class, array(
            'data' => wp_create_nonce(Plugin::NAME .'-save-settings'),
            'constraints' => array(
                new Constraints\NotBlank(),
                new WordPressNonceConstraint(array('name' => Plugin::NAME .'-save-settings')),
            ),
        ));

        $builder
            ->add('submit', Type\SubmitType::class, array(
                'label' => __('Save Changes', Plugin::NAME),
                'attr' => array('class' => 'button button-primary'),
            ))
        ;
    }
}

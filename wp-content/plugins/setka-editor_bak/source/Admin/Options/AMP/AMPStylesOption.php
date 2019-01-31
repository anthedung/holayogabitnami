<?php
namespace Setka\Editor\Admin\Options\AMP;

use Korobochkin\WPKit\Options\Special\AbstractAggregateOption;
use Setka\Editor\Plugin;
use Symfony\Component\Validator\Constraints;

class AMPStylesOption extends AbstractAggregateOption
{
    /**
     * AMPStylesOption constructor.
     */
    public function __construct()
    {
        $this
            ->setName(Plugin::_NAME_ . '_amp_styles')
            ->setAutoload(true)
            ->setDefaultValue(array(
                'common' => array(),
                'themes' => array(),
                'layouts' => array(),
            ));
    }

    /**
     * @inheritdoc
     */
    public function buildConstraint()
    {
        return new Constraints\Collection(array(
            'fields' => array(

                'common' => array(
                    new Constraints\NotBlank(),
                    new Constraints\All(array(
                        'constraints' => array(
                            new Constraints\NotBlank(),
                            new Constraints\Collection(array(
                                'fields' => array(
                                    'id' => array(
                                        new Constraints\NotBlank(),
                                    ),
                                    'url' => array(
                                        new Constraints\NotBlank(),
                                        new Constraints\Url(),
                                    ),
                                    'filetype' => array(
                                        new Constraints\NotBlank(),
                                        new Constraints\Choice(array(
                                            'choices' => array('css'),
                                            'strict' => true,
                                        )),
                                    ),
                                ),
                                'allowExtraFields' => true,
                            )),
                        ),
                    )),
                ),

                'themes' => array(
                    new Constraints\NotBlank(),
                    new Constraints\All(array(
                        'constraints' => array(
                            new Constraints\NotBlank(),
                            new Constraints\Collection(array(
                                'fields' => array(
                                    'id' => array(
                                        new Constraints\NotBlank(),
                                        new Constraints\Type(array(
                                            'type' => 'string',
                                        )),
                                    ),
                                    'url' => array(
                                        new Constraints\NotBlank(),
                                        new Constraints\Url(),
                                    ),
                                    'filetype' => array(
                                        new Constraints\NotBlank(),
                                        new Constraints\Choice(array(
                                            'choices' => array('css'),
                                            'strict' => true,
                                        )),
                                    ),
                                    'fonts' => new Constraints\Optional(new Constraints\All(array(
                                        'constraints' => array(
                                            new Constraints\NotBlank(),
                                            new Constraints\Url(),
                                        ),
                                    ))),
                                ),
                                'allowExtraFields' => true,
                            )),
                        ),
                    )),
                ),

                'layouts' => array(
                    new Constraints\NotBlank(),
                    new Constraints\All(array(
                        'constraints' => array(
                            new Constraints\NotBlank(),
                            new Constraints\Collection(array(
                                'fields' => array(
                                    'id' => array(
                                        new Constraints\NotBlank(),
                                        new Constraints\Type(array(
                                            'type' => 'string',
                                        )),
                                    ),
                                    'url' => array(
                                        new Constraints\NotBlank(),
                                        new Constraints\Url(),
                                    ),
                                    'filetype' => array(
                                        new Constraints\NotBlank(),
                                        new Constraints\Choice(array(
                                            'choices' => array('css'),
                                            'strict' => true,
                                        )),
                                    ),
                                ),
                                'allowExtraFields' => true,
                            )),
                        ),
                    )),
                ),
            ),
            'allowExtraFields' => true,
        ));
    }
}

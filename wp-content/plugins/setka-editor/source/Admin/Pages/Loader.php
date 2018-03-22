<?php
namespace Setka\Editor\Admin\Pages;

use Symfony\Component\Form\FormFactoryInterface;
use Setka\Editor\Admin\Options;

class Loader
{

    /**
     * @var \Twig_Environment
     */
    public static $symfony_twig;

    /**
     * @var FormFactoryInterface
     */
    public static $symfony_form_factory;
}

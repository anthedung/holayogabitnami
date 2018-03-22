<?php
namespace Setka\Editor\Admin\Pages;

use Korobochkin\WPKit\Pages\PageInterface;
use Korobochkin\WPKit\Pages\Views\PageViewInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class AdminPages
 */
class AdminPages
{
    /**
     * @var PageInterface[]
     */
    protected $pages = array();

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var PageViewInterface
     */
    protected $pageView;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * AdminPages constructor.
     *
     * @param $twig \Twig_Environment
     * @param $formFactory FormFactoryInterface
     * @param $pages array
     */
    public function __construct(\Twig_Environment $twig, FormFactoryInterface $formFactory, $pages)
    {
        $this->twig        = $twig;
        $this->formFactory = $formFactory;
        $this->pages       = $pages;
        $this->initializePages();
    }

    protected function initializePages()
    {
        $pagesAssoc = array();
        foreach ($this->pages as $page) {
            $pagesAssoc[$page->getName()] = $page;
            $page->getView()->setTwigEnvironment($this->twig);
        }
        $this->pages = $pagesAssoc;
    }

    public function register()
    {
        foreach ($this->pages as $page) {
            $page->register();
        }
    }
}

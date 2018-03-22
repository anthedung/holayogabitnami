<?php
namespace Setka\Editor\Admin\Pages;

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Form;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;

class TwigFactory
{
    /**
     * @var string|false Path to folder with cache files or false if cache disabled.
     */
    protected $cache = false;

    /**
     * @var string Path to folder with Twig templates.
     */
    protected $templatesPath;

    /**
     * TwigFactory constructor.
     *
     * @param false|string $cache
     * @param string $templatesPath
     */
    public function __construct($cache, $templatesPath)
    {
        $this->cache         = $cache;
        $this->templatesPath = $templatesPath;
    }

    /**
     * Creates \Twig_Environment instance.
     *
     * @throws \ReflectionException
     *
     * @return \Twig_Environment
     */
    public function create()
    {
        if ($this->cache) {
            $cacheTranslations = $this->cache . 'translate/';
            $cacheTwig         = $this->cache . 'twig/';
        } else {
            $cacheTranslations = null;
            $cacheTwig         = false;
        }

        $translator = new Translator('en', null, $cacheTranslations);
        $translator->addLoader('xlf', new XliffFileLoader());

        $reflection = new \ReflectionClass(Form::class);
        $translator->addResource(
            'xlf',
            dirname($reflection->getFileName()) . '/Resources/translations/validators.ru.xlf',
            'en',
            'validators'
        );

        $reflection = new \ReflectionClass(TwigRenderer::class);

        $twig = new \Twig_Environment(
            new \Twig_Loader_Filesystem(array(
                $this->templatesPath,
                dirname(dirname($reflection->getFileName())) . '/Resources/views/Form',
            )),
            array(
                'cache' => $cacheTwig,
            )
        );

        $formEngine = new TwigRendererEngine(array('form_div_layout.html.twig'), $twig);
        $twig->addRuntimeLoader(new \Twig_FactoryRuntimeLoader(array(
            TwigRenderer::class => function () use ($formEngine) {
                return new TwigRenderer($formEngine);
            }
        )));
        $twig->addExtension(new FormExtension());
        $twig->addExtension(new TranslationExtension($translator));

        return $twig;
    }
}

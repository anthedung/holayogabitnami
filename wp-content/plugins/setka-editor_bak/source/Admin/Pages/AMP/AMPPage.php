<?php
namespace Setka\Editor\Admin\Pages\AMP;

use Korobochkin\WPKit\Pages\SubMenuPage;
use Korobochkin\WPKit\Pages\Views\TwigPageView;
use Korobochkin\WPKit\Utils\RequestFactory;
use Setka\Editor\Admin\Options\AMP\AMPCssOption;
use Setka\Editor\Admin\Options\AMP\AMPFontsOption;
use Setka\Editor\Admin\Prototypes\Pages\PrepareTabsTrait;
use Setka\Editor\Plugin;

class AMPPage extends SubMenuPage
{
    use PrepareTabsTrait;

    /**
     * @var AMPCssOption
     */
    protected $ampCssOption;

    /**
     * @var AMPFontsOption
     */
    protected $ampFontsOption;

    public function __construct()
    {
        $this->setParentSlug(Plugin::NAME);
        $this->setPageTitle(__('AMP', Plugin::NAME));
        $this->setMenuTitle($this->getPageTitle());
        $this->setCapability('manage_options');
        $this->setMenuSlug(Plugin::NAME . '-amp');

        $this->setName('amp');

        $view = new TwigPageView();
        $view->setTemplate('admin/settings/amp/page.html.twig');
        $this->setView($view);
    }

    /**
     * @inheritdoc
     */
    public function lateConstruct()
    {
        $form = $this->getFormFactory()->create(AMPType::class, array(
            'amp_css' => $this->getAMPCssOption()->get(),
            'amp_fonts' => implode(PHP_EOL, $this->getAMPFontsOption()->get()),
        ));
        $this->setForm($form);

        $this->handleRequest();

        $attributes = array(
            'page' => $this,
            'form' => $this->getForm()->createView(),
            'translations' => array(
                'amp_css_description' => __('CSS code for AMP pages.', Plugin::NAME),
                'amp_fonts_description' => __('Fonts URLs for AMP pages (one URL per line).'),
            ),
        );

        $this->enqueueCodeEditor();

        $this->getView()->setContext($attributes);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function handleRequest()
    {
        $request = RequestFactory::create();
        $this->form->handleRequest($request);

        if ($this->form->isSubmitted()) {
            if ($this->form->isValid()) {
                $data = $this->form->getData();

                $result = str_replace("\r\n", PHP_EOL, $data['amp_fonts']);
                $result = trim($result);

                $data['amp_fonts'] = explode(PHP_EOL, $result);

                if($this->getAMPFontsOption()->set($data['amp_fonts'])->isValid()) {
                    $this->getAMPFontsOption()->flush();
                }

                $this->getAMPCssOption()->updateValue($data['amp_css']);
            }
        }
    }

    /**
     * @return AMPCssOption
     */
    public function getAMPCssOption()
    {
        return $this->ampCssOption;
    }

    /**
     * @param AMPCssOption $ampCssOption
     * @return $this
     */
    public function setAMPCssOption(AMPCssOption $ampCssOption)
    {
        $this->ampCssOption = $ampCssOption;
        return $this;
    }

    /**
     * @return AMPFontsOption
     */
    public function getAMPFontsOption()
    {
        return $this->ampFontsOption;
    }

    /**
     * @param AMPFontsOption $ampFontsOption
     * @return $this
     */
    public function setAMPFontsOption(AMPFontsOption $ampFontsOption)
    {
        $this->ampFontsOption = $ampFontsOption;
        return $this;
    }

    /**
     * @return $this
     */
    public function enqueueCodeEditor()
    {
        if(function_exists('wp_enqueue_code_editor')) {
            wp_enqueue_code_editor(array(
                'type' => 'css',
            ));
        }
        return $this;
    }
}

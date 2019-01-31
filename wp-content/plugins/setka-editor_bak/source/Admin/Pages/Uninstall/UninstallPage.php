<?php
namespace Setka\Editor\Admin\Pages\Uninstall;

use Korobochkin\WPKit\Pages\SubMenuPage;
use Korobochkin\WPKit\Pages\Views\TwigPageView;
use Setka\Editor\Admin\Prototypes\Pages\PrepareTabsTrait;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;

/**
 * Class UninstallPage
 */
class UninstallPage extends SubMenuPage
{
    use PrepareTabsTrait;

    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    public function __construct()
    {
        $this->setParentSlug(Plugin::NAME);
        $this->setPageTitle(_x('Uninstall', 'Uninstall page title', Plugin::NAME));
        $this->setMenuTitle($this->getPageTitle());
        $this->setCapability('manage_options');
        $this->setMenuSlug(Plugin::NAME . '-uninstall');

        $this->setName('uninstall');

        $view = new TwigPageView();
        $view->setTemplate('admin/settings/uninstall/page.html.twig');
        $this->setView($view);
    }

    /**
     * @inheritdoc
     */
    public function lateConstruct()
    {
        $this->prepareTabs();

        $attributes = array(
            'page' => $this,
            'translations' => array(
                'keep_styles_title' => __('Keep post styles', Plugin::NAME),
                'keep_styles_text' => __('Keep Setka Editor post styles even after you delete Setka Editor plugin. Just add the following code into your WordPress theme <code>functions.php</code>.', Plugin::NAME),
                'keep_styles_caption' => __('Attention: Please follow these instructions only if you are going to delete the plugin.', Plugin::NAME),
            ),
        );

        $attributes['assets'] = $this->lateConstructAssets();

        $this->enqueueCodeEditor();

        $this->getView()->setContext($attributes);

        return $this;
    }

    /**
     * @return array
     */
    protected function lateConstructAssets()
    {
        $local = $this->getSetkaEditorAccount()->isLocalFilesUsage();

        $links = array();

        if($local) {
            $option = $this->getSetkaEditorAccount()->getThemeResourceCSSLocalOption();
        } else {
            $option = $this->getSetkaEditorAccount()->getThemeResourceCSSOption();
        }

        $links['css'] = $option->get();

        $links['js'] = $this->getSetkaEditorAccount()->getThemePluginsJSOption()->get();

        return $links;
    }

    /**
     * @return SetkaEditorAccount
     */
    public function getSetkaEditorAccount()
    {
        return $this->setkaEditorAccount;
    }

    /**
     * @param SetkaEditorAccount $setkaEditorAccount
     *
     * @return $this
     */
    public function setSetkaEditorAccount(SetkaEditorAccount $setkaEditorAccount)
    {
        $this->setkaEditorAccount = $setkaEditorAccount;
        return $this;
    }

    /**
     * @return $this
     */
    public function enqueueCodeEditor()
    {
        if(function_exists('wp_enqueue_code_editor')) {
            wp_enqueue_code_editor(array(
                'type' => 'php',
            ));
        }
        return $this;
    }
}

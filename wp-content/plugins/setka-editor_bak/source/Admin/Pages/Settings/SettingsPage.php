<?php
namespace Setka\Editor\Admin\Pages\Settings;

use Korobochkin\WPKit\Notices\NoticesStack;
use Korobochkin\WPKit\Options\OptionInterface;
use Korobochkin\WPKit\Pages\SubMenuPage;
use Korobochkin\WPKit\Pages\Views\TwigPageView;
use Setka\Editor\Admin\Notices\SettingsSavedSuccessfullyNotice;
use Setka\Editor\Admin\Prototypes\Pages\PrepareTabsTrait;
use Setka\Editor\Plugin;
use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\User\Capabilities;
use Setka\Editor\Service\DataFactory;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SettingsPage
 */
class SettingsPage extends SubMenuPage
{
    use PrepareTabsTrait;

    /**
     * @var NoticesStack
     */
    protected $noticesStack;

    /**
     * @var DataFactory
     */
    protected $dataFactory;

    public function __construct()
    {
        $this->setParentSlug(Plugin::NAME);
        $this->setPageTitle(_x('Settings', 'Settings page title', Plugin::NAME));
        $this->setMenuTitle($this->getPageTitle());
        $this->setCapability('manage_options');
        $this->setMenuSlug(Plugin::NAME . '-settings');

        $this->setName('settings');

        $view = new TwigPageView();
        $view->setTemplate('admin/settings/settings/page.html.twig');
        $this->setView($view);
    }

    public function lateConstruct()
    {
        $this->prepareTabs();

        $form = $this->getFormFactory()->create(SettingsType::class, array(
            'post_types' => $this->getDataFactory()->create(Options\EditorAccessPostTypesOption::class)->get(),
            'white_label' => $this->getDataFactory()->create(Options\WhiteLabelOption::class)->get(),
        ));
        $this->setForm($form);

        $this->handleRequest();

        $attributes = array(
            'page' => $this,
            'form' => $this->getForm()->createView(),
            'translations' => array(
                'post_types_description' => __('Enable Setka Editor for the following post types. You can also disable Setka Editor for all post types by unchecking all checkboxes below. In this case all posts created with Setka Editor continue working and displaying correctly but you will not be able to create a new post with Setka Editor.', Plugin::NAME),
                'roles_description' => __('Enable Setka Editor for the selected User Roles. You can also add or remove this permission by simply adding or removing %1$s capability to any User Role with <a href="https://wordpress.org/plugins/members/">Members</a> plugin.', Plugin::NAME),
                'roles_capability' => Capabilities\UseEditorCapability::NAME,
                'white_label' => __('Show “Created with Setka Editor” credits below the content', Plugin::NAME),
            )
        );

        $this->getView()->setContext($attributes);
    }

    /**
     * @return $this
     */
    public function handleRequest()
    {
        $request = Request::createFromGlobals();
        $this->form->handleRequest($request);

        if(!$this->form->isSubmitted() || !$this->form->isValid()) {
            return $this;
        }

        $data = $this->form->getData();

        /**
         * @var $editorAccessPostTypesOption OptionInterface
         */
        $editorAccessPostTypesOption = $this->getDataFactory()->create(Options\EditorAccessPostTypesOption::class);
        $editorAccessPostTypesOption->updateValue($data['post_types']);

        $roles = get_editable_roles();
        if(is_array($roles) && is_array($data['roles'])) {
            foreach($roles as $roleKey => $roleValue) {
                $role = get_role($roleKey);
                if(array_search($roleKey, $data['roles'], true) === false) {
                    $role->remove_cap(Capabilities\UseEditorCapability::NAME);
                } elseif(!$role->has_cap(Capabilities\UseEditorCapability::NAME)) {
                    $role->add_cap(Capabilities\UseEditorCapability::NAME);
                }
            }
        }
        unset($roles, $role, $roleKey, $roleValue);

        /**
         * @var $whiteLabelOption OptionInterface
         */
        $whiteLabelOption = $this->getDataFactory()->create(Options\WhiteLabelOption::class);
        $whiteLabelOption->updateValue($data['white_label']);

        $this->getNoticesStack()->addNotice(new SettingsSavedSuccessfullyNotice());

        return $this;
    }

    /**
     * @return NoticesStack
     */
    public function getNoticesStack()
    {
        return $this->noticesStack;
    }

    /**
     * @param NoticesStack $noticesStack
     *
     * @return $this
     */
    public function setNoticesStack(NoticesStack $noticesStack)
    {
        $this->noticesStack = $noticesStack;
        return $this;
    }

    /**
     * @return DataFactory
     */
    public function getDataFactory()
    {
        return $this->dataFactory;
    }

    /**
     * @param DataFactory $dataFactory
     *
     * @return $this
     */
    public function setDataFactory(DataFactory $dataFactory)
    {
        $this->dataFactory = $dataFactory;
        return $this;
    }

    /**
     * @return FormFactoryInterface
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * @param FormFactoryInterface $formFactory
     *
     * @return $this
     */
    public function setFormFactory(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }
}

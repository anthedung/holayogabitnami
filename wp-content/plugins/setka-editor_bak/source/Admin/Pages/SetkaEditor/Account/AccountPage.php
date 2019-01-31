<?php
namespace Setka\Editor\Admin\Pages\SetkaEditor\Account;

use Korobochkin\WPKit\Pages\MenuPage;
use Korobochkin\WPKit\Pages\Views\TwigPageView;
use Setka\Editor\Admin\Prototypes\Pages\PrepareTabsTrait;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;
use Setka\Editor\Admin\Options;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AccountPage
 */
class AccountPage extends MenuPage
{
    use PrepareTabsTrait;

    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    public function __construct()
    {
        $this->setPageTitle(__('Account', Plugin::NAME));
        $this->setMenuTitle($this->getPageTitle());
        $this->setCapability('manage_options');
        $this->setMenuSlug(Plugin::NAME);

        $this->setName('account');

        $view = new TwigPageView();
        $view->setTemplate('admin/settings/setka-editor/account/page.html.twig');
        $this->setView($view);
    }

    public function lateConstruct()
    {
        $this->prepareTabs();

        $this->setFormEntity(new SignIn());
        $this->lateConstructEntity();
        $this->setForm($this->getFormFactory()->createNamed(Plugin::_NAME_, SignInType::class, $this->getFormEntity()));

        $this->handleRequest();

        $attributes = array(
            'page' => $this,
            'form' => $this->getForm()->createView(),
            'translations' => array(
                'already_signed_in' => __('You have already started the plugin.', Plugin::NAME),
            ),
            'signedIn' => $this->getSetkaEditorAccount()->isLoggedIn(),
        );

        $this->getView()->setContext($attributes);

        return $this;
    }

    public function handleRequest()
    {
        $form = $this->getForm()->handleRequest(Request::createFromGlobals());

        if($form->isSubmitted()) {
            if($form->isValid()) {
                if($form->get('sync')->isClicked()) {
                    $this->getSetkaEditorAccount()->getSignIn()->signInByToken(
                        $this->getSetkaEditorAccount()->getTokenOption()->get(),
                        false
                    );
                } elseif($form->get('submitToken')->isClicked()) {
                    $this->getSetkaEditorAccount()->signOut();
                    $this->getSetkaEditorAccount()->getSignIn()->signInAnonymous();
                    $url = $this->getURL();
                    $url = add_query_arg('account-type', 'sign-in', $url);
                    wp_safe_redirect($url);
                    exit();
                }
            }
        }
    }

    protected function lateConstructEntity()
    {
        /**
         * @var $a SignIn
         */
        $a = $this->getFormEntity();

        $token = new Options\TokenOption();
        $a->setToken($token->get());
    }

    /**
     * @inheritdoc
     */
    public function getURL()
    {
        return add_query_arg(
            'page',
            $this->getMenuSlug(),
            admin_url('admin.php')
        );
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

<?php
namespace Setka\Editor\Admin\Pages\SetkaEditor\Account;

use Korobochkin\WPKit\Pages\MenuPage;
use Korobochkin\WPKit\Pages\Views\TwigPageView;
use Setka\Editor\Admin\Prototypes\Pages\PrepareTabsTrait;
use Setka\Editor\Plugin;
use Setka\Editor\Admin\Pages\Loader;
use Setka\Editor\Service\SetkaAccount\Account;
use Setka\Editor\Admin\Options;
use Setka\Editor\Service\SetkaAccount\SignOut;
use Symfony\Component\HttpFoundation\Request;

class AccountPage extends MenuPage
{
    use PrepareTabsTrait;

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
        $this->setForm(Loader::$symfony_form_factory->createNamed(Plugin::_NAME_, SignInType::class, $this->getFormEntity()));

        $this->handleRequest();

        $attributes = array(
            'page' => $this,
            'form' => $this->getForm()->createView(),
            'translations' => array(
                'already_signed_in' => __('You have already started the plugin.', Plugin::NAME),
            ),
            'signedIn' => Account::isLoggedIn(),
        );

        $this->getView()->setContext($attributes);
    }

    public function handleRequest()
    {
        $form = $this->getForm()->handleRequest(Request::createFromGlobals());

        if($form->isSubmitted()) {
            if($form->isValid()) {
                SignOut::signOutAction();
                \Setka\Editor\Service\SetkaAccount\SignIn::signInAnonymous();
                $url = $this->getURL();
                $url = add_query_arg('account-type', 'sign-in', $url);
                wp_safe_redirect($url);
                exit();
            }
        }
    }

    protected function lateConstructEntity()
    {
        /**
         * @var $a SignIn
         */
        $a = $this->getFormEntity();

        $token = new Options\Token\Option();
        $a->setToken($token->getValue());
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
}

<?php
namespace Setka\Editor\Admin\Pages\SetkaEditor\SignUp;

use Korobochkin\WPKit\Pages\MenuPage;
use Korobochkin\WPKit\Pages\Views\TwigPageView;
use Setka\Editor\Admin\Notices\NoticesStack;
use Setka\Editor\Admin\Notices\SignUpErrorNotice;
use Setka\Editor\Admin\Notices\SuccessfulSignUpNotice;
use Setka\Editor\Admin\Prototypes\Notices\ErrorNotice;
use Setka\Editor\Admin\Prototypes\Pages\PrepareTabsTrait;
use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Plugin;
use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Pages\Loader;
use Setka\Editor\Admin\Transients;
use Setka\Editor\Prototypes\Errors\ErrorInterface;
use Setka\Editor\Service\Countries\Countries;
use Setka\Editor\Service\SetkaAccount\Account;
use Setka\Editor\Service\SetkaAccount\SignIn;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class SignUpPage extends MenuPage
{
    use PrepareTabsTrait;

    /**
     * @var string
     */
    protected $processState = '';

    /**
     * @var NoticesStack
     */
    protected $noticesStack;

    public function __construct()
    {
        $this->setPageTitle(__('Register a Setka Editor account to modify your post styles', Plugin::NAME));
        $this->setMenuTitle(_x('Register', 'Menu title', Plugin::NAME));
        $this->setCapability('manage_options');
        $this->setMenuSlug(Plugin::NAME);

        $this->setName('sign-up');

        $view = new TwigPageView();
        $view->setTemplate('admin/settings/setka-editor/page.html.twig');
        $this->setView($view);
    }

    /**
     * @inheritdoc
     */
    public function lateConstruct()
    {
        $this->prepareTabs();

        $this->setRequest(Request::createFromGlobals());

        $this->setFormEntity(new SignUp());
        $this->lateConstructEntity();

        $formBuilder = Loader::$symfony_form_factory->createNamedBuilder(Plugin::_NAME_, SignUpType::class, $this->getFormEntity());
        $form        = $formBuilder
            ->setAction($this->getURL())
            ->getForm();
        $this->setForm($form);

        $this->handleRequest();

        if('sign-up-success' === $this->processState) {
            /**
             * @var $data SignUp
             */
            $data = $this->getFormEntity();
            $data->setAccountType('sign-in');

            $formBuilder = Loader::$symfony_form_factory->createNamedBuilder(Plugin::_NAME_, SignUpType::class, $this->getFormEntity());
            $formBuilder->setAction($this->getURL());
            $form = $formBuilder->getForm();
            $this->setForm($form);
        }

        $attributes = array(
            'page' => $this,
            'form' => $form->createView(),
            'translations' => array(
                'start' => __('Sign up for Setka Editor Free plan to create your own post style with branded fonts, colors and other visual elements and customized grid system.', Plugin::NAME),
                'email_sub_label' => __('We will send a license key to this address', Plugin::NAME),
                'password_sub_label' => __('To have access to Style Manager', Plugin::NAME),
                'terms_and_conditions' => '<a href="https://editor.setka.io/terms/Terms-and-Conditions-Setka-Editor.pdf" target="_blank">Terms and Conditions</a>',
                'privacy_policy' => '<a href="https://editor.setka.io/terms/Privacy-Policy-Setka-Editor.pdf" target="_blank">Privacy Policy</a>',
                'already_signed_in' => __('You have already started the plugin.', Plugin::NAME),
            ),
            'signedIn' => Account::isLoggedIn(),
        );

        $this->getView()->setContext($attributes);
    }

    /**
     * @inheritdoc
     */
    public function handleRequest()
    {
        $form = $this->getForm()->handleRequest($this->getRequest());

        if($form->isSubmitted()) {
            if($form->isValid()) {
                $data = $form->getData();
                /**
                 * @var $data SignUp
                 */
                if('sign-in' === $data->getAccountType()) {
                    $this->handleRequestSignIn();
                } else {
                    $this->handleRequestSignUp();
                }
            } else {
                // Show errors on the page
                // Actually Symfony show the errors automatically near each field
            }
        }
    }

    public function handleRequestSignIn()
    {
        /**
         * @var $data SignUp
         */
        $form = $this->getForm();
        $data = $form->getData();

        $option = new Options\Token\Option();
        $errors = $option->getValidator()->validate($data->getToken(), $option->getConstraint());

        if(count($errors) !== 0) {
            $field = $form->get('token');
            foreach($errors as $error) {
                $field->addError(new FormError($error->getMessage()));
            }
            return;
        }
        unset($option, $errors);

        $results = SignIn::signInByToken($data->getToken());

        $haveErrors = false;
        foreach($results as $action) {
            if($action->getErrors()->hasErrors()) {
                $haveErrors = true;
                $field      = $form->get('token');
                // TODO: we adding errors twice (if similar errors exists in each action)
                foreach($action->getErrors() as $error) {
                    /**
                     * @var $error \Setka\Editor\Prototypes\Errors\Error
                     */
                    $field->addError(new FormError($error->getMessage()));
                }
            }
        }

        if(!$haveErrors) {
            $transient = new Transients\AfterSignInNotice\Transient();
            $transient->updateValue(1);

            wp_safe_redirect($this->getURL());
            exit();
        }
    }

    public function handleRequestSignUp()
    {
        $form   = $this->getForm();
        $api    = SetkaAPI\APIFactory::create();
        $action = new SetkaAPI\Actions\SignUpAction();

        $fieldsMap = array(
            // API => Form
            // person and company
            'company_type' => 'accountType',

            // person and company
            'email' => 'email',
            'first_name' => 'firstName',
            'last_name' => 'lastName',
            'region' => 'region',
            'password' => 'password',
            'company_domain' => 'companyDomain',

            // company
            'company_name' => 'companyName',
            'company_size' => 'companySize',
            'company_department' => 'companyDepartment',
        );

        /**
         * Prepare data
         * @var $a SignUp
         */
        $a              = $form->getData();
        $requestDetails = array(
            'body' => array(
                'signup' => array(
                    'company_type'       => $a->getAccountType(),
                    'email'              => $a->getEmail(),
                    'first_name'         => $a->getFirstName(),
                    'last_name'          => $a->getLastName(),
                    'region'             => $a->getRegion(),
                    'password'           => $a->getPassword(),
                    'company_domain'     => $a->getCompanyDomain(),
                    'company_department' => $a->getCompanyDepartment()->getValue(),
                ),
            ),
        );
        if('company' === $a->getAccountType()) {
            $requestDetails['body']['signup']['company_name'] = $a->getCompanyName();
            $requestDetails['body']['signup']['company_size'] = $a->getCompanySize()->getValue();
        }

        $theme = wp_get_theme();
        if('vivaro' === $theme->get_stylesheet()) {
            $requestDetails['body']['signup']['partner_vivaro'] = true;
        }

        $action->setRequestDetails($requestDetails);
        unset($requestDetails);
        $action->configureAndResolveRequestDetails();

        $api->request($action);

        if($action->getErrors()->hasErrors()) {
            $this->getNoticesStack()->addNotice(new SignUpErrorNotice());
            foreach($action->getErrors() as $error) {
                /**
                 * @var $error ErrorInterface
                 */
                $notice = new ErrorNotice(Plugin::NAME, $error->getCode());
                $notice->setContent('<p>' . $error->getMessageHTML() .'</p>');
                $this->getNoticesStack()->addNotice($notice);
            }
            unset($error, $notice);
        } else {
            $response = $action->getResponse();
            switch($response->getStatusCode()) {
                case $response::HTTP_CREATED:
                    $whiteLabelOption = new Options\WhiteLabel\WhiteLabelOption();
                    $whiteLabel       = $a->isWhiteLabel();
                    if($whiteLabel) {
                        $whiteLabel = '1';
                    } else {
                        $whiteLabel = '0';
                    }
                    $whiteLabelOption->updateValue($whiteLabel);

                    $this->getNoticesStack()->addNotice(new SuccessfulSignUpNotice());
                    $this->processState = 'sign-up-success';
                    break;

                case $response::HTTP_UNPROCESSABLE_ENTITY:
                    if($response->getContent()->has('error')) {
                        $error  = $response->getContent()->has('error');
                        $notice = new ErrorNotice(Plugin::NAME, 'setka_api_error');
                        $notice->setContent('<p>' . esc_html($error) .'</p>');
                        $this->getNoticesStack()->addNotice($notice);
                        unset($error, $notice);
                    } elseif ($response->getContent()->has('errors')) {
                        $errors = $response->getContent()->get('errors');

                        foreach($errors as $errorKey => &$errorValue) {
                            if(is_array($errorValue)) {
                                foreach($errorValue as $errorCode => &$errorMessage) {
                                    if(isset($fieldsMap[$errorKey])) {
                                        $field = $form->get($fieldsMap[$errorKey]);
                                    } else {
                                        $field = $form;
                                    }

                                    if('email' === $errorKey && 'has already been taken' === $errorMessage) {
                                        $errorMessage = __('This email has already been taken to create Setka Editor account. Please reset password on editor.setka.io or enter another email.', Plugin::NAME);
                                    }

                                    // We can't add html markup to errors since form_errors block simply using
                                    // message attribute from FormError instance and escaping before output.
                                    $field->addError(new FormError($errorMessage));
                                }
                            }
                        }
                        unset($errors, $errorKey, $errorValue, $errorCode, $errorMessage, $notice);
                    }
                    break;
            }
        }
    }

    protected function lateConstructEntity()
    {
        /**
         * @var $a SignUp
         */
        $a    = $this->getFormEntity();
        $user = wp_get_current_user();

        $a->setAccountType('person');

        if($this->getRequest()->query->has('account-type')) {
            $accountType = $this->getRequest()->query->get('account-type');
            if(in_array(
                $accountType,
                array(
                    'person', 'company', 'sign-in',
                ),
                true
            )) {
                $a->setAccountType($accountType);
            }
        }

        $firstName = $user->get('first_name');
        if(is_string($firstName)) {
            $a->setFirstName($firstName);
        }
        unset($firstName);

        $lastName = $user->get('last_name');
        if(is_string($lastName)) {
            $a->setLastName($lastName);
        }
        unset($lastName);

        $a->setRegion(Countries::getCountryFromWPLocale(get_locale()));

        $a->setCompanyDomain(site_url());

        $a->setTermsAndConditions(false);

        $whiteLabel = new Options\WhiteLabel\WhiteLabelOption();
        $whiteLabel = $whiteLabel->getValue();
        if('1' === $whiteLabel) {
            $whiteLabel = true;
        } else {
            $whiteLabel = false;
        }
        $a->setWhiteLabel($whiteLabel);

        if(Account::isLoggedIn()) {
            $token = new Options\Token\Option();
            $a->setToken($token->getValue());
        }
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
}

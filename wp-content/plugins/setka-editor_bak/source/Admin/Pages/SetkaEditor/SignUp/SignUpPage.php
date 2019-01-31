<?php
namespace Setka\Editor\Admin\Pages\SetkaEditor\SignUp;

use Korobochkin\WPKit\Notices\Notice;
use Korobochkin\WPKit\Notices\NoticeErrorView;
use Korobochkin\WPKit\Notices\NoticesStack;
use Korobochkin\WPKit\Pages\MenuPage;
use Korobochkin\WPKit\Pages\Views\TwigPageView;
use Setka\Editor\Admin\Notices\SignUpErrorNotice;
use Setka\Editor\Admin\Notices\SuccessfulSignUpNotice;
use Setka\Editor\Admin\Prototypes\Pages\PrepareTabsTrait;
use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Setka\Editor\Plugin;
use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Transients;
use Setka\Editor\Service\Countries\Countries;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class SignUpPage
 */
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

    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * @var SetkaEditorAPI\API
     */
    protected $setkaEditorAPI;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

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

        $formBuilder = $this->getFormFactory()->createNamedBuilder(Plugin::_NAME_, SignUpType::class, $this->getFormEntity());
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

            $formBuilder = $this->getFormFactory()->createNamedBuilder(Plugin::_NAME_, SignUpType::class, $this->getFormEntity());
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
            'signedIn' => $this->getSetkaEditorAccount()->isLoggedIn(),
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

        $results    = $this->getSetkaEditorAccount()->getSignIn()->signInByToken($data->getToken());
        $violations = new ConstraintViolationList();

        $this->optimizeActionViolations($results, $violations);

        if(count($violations) !== 0) {
            $field = $form->get('token');
            foreach ($violations as $violation) {
                $field->addError(new FormError(
                    $violation->getMessage(),
                    $violation->getMessageTemplate(),
                    $violation->getParameters(),
                    $violation->getPlural()
                ));
            }
        } else {
            $transient = new Transients\AfterSignInNoticeTransient();
            $transient->updateValue(true);

            wp_safe_redirect($this->getURL());
            exit();
        }
    }

    /**
     * Merge errors of all actions and insert single ResponseBodyInvalidError instead of many similar errors.
     *
     * @param array $results List of actions.
     * @param ConstraintViolationListInterface $violations Violations list.
     *
     * @see ResponseBodyInvalidError
     *
     * @return $this For chain calls.
     */
    public function optimizeActionViolations(array $results, ConstraintViolationListInterface $violations)
    {
        $bodyError = false;

        foreach($results as $action) {
            foreach ($action->getErrors() as $actionViolation) {
                if(get_class($actionViolation) === ConstraintViolation::class) {
                    $bodyError = true;
                } else {
                    /**
                     * @var $actionViolation ConstraintViolationInterface
                     */
                    $violations->set($actionViolation->getCode(), $actionViolation);
                }
            }
        }

        if($bodyError) {
            $violations->set(
                SetkaEditorAPI\Errors\ResponseBodyInvalidError::class,
                new SetkaEditorAPI\Errors\ResponseBodyInvalidError()
            );
        }

        return $this;
    }

    public function handleRequestSignUp()
    {
        $form   = $this->getForm();
        $action = new SetkaEditorAPI\Actions\SignUpAction();

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

        $this->setkaEditorAPI->request($action);

        if(count($action->getErrors()) !== 0) {
            $this->getNoticesStack()->addNotice(new SignUpErrorNotice());
            foreach($action->getErrors() as $error) {
                /**
                 * @var $error ConstraintViolationInterface
                 */
                $notice = new Notice();
                $notice->setName(Plugin::NAME . '_' .$error->getCode());
                $notice->setContent('<p>' . $error->getMessage() .'</p>');
                $notice->setView(new NoticeErrorView());
                $this->getNoticesStack()->addNotice($notice);
            }
            unset($error, $notice);
        } else {
            $response = $action->getResponse();
            switch($response->getStatusCode()) {
                case $response::HTTP_CREATED:
                    $whiteLabelOption = new Options\WhiteLabelOption();
                    $whiteLabel       = $a->isWhiteLabel();
                    $whiteLabelOption->updateValue($whiteLabel);
                    $this->getNoticesStack()->addNotice(new SuccessfulSignUpNotice());
                    $this->processState = 'sign-up-success';
                    break;

                case $response::HTTP_UNPROCESSABLE_ENTITY:
                    if($response->getContent()->has('error')) {
                        $error  = $response->getContent()->has('error');
                        $notice = new Notice();
                        $notice->setName(Plugin::NAME . '_setka_api_error');
                        $notice->setContent('<p>' . esc_html($error) .'</p>');
                        $notice->setView(new NoticeErrorView());
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

        $whiteLabel = new Options\WhiteLabelOption();
        $whiteLabel = $whiteLabel->get();
        if($whiteLabel) {
            $whiteLabel = true;
        } else {
            $whiteLabel = false;
        }
        $a->setWhiteLabel($whiteLabel);

        if($this->getSetkaEditorAccount()->isLoggedIn()) {
            $token = new Options\TokenOption();
            $a->setToken($token->get());
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
     * @return SetkaEditorAPI\API
     */
    public function getSetkaEditorAPI()
    {
        return $this->setkaEditorAPI;
    }

    /**
     * @param SetkaEditorAPI\API $setkaEditorAPI
     * @return $this
     */
    public function setSetkaEditorAPI(SetkaEditorAPI\API $setkaEditorAPI)
    {
        $this->setkaEditorAPI = $setkaEditorAPI;
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

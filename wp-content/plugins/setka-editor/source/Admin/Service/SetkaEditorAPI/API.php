<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI;

use Setka\Editor\Admin\Service\SetkaEditorAPI\Errors\ResponseError;
use Setka\Editor\Admin\Service\SetkaEditorAPI\Prototypes\ActionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SetkaEditorAPI
 */
class API
{
    /**
     * @var AuthCredits
     */
    protected $authCredits;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var ActionInterface
     */
    protected $action;

    /**
     * @var ClientInterface Interface to send HTTP requests
     */
    protected $client;

    /**
     * @var array {
     *      Options used by API.
     *      string $app_version WordPress version.
     *      string $plugin_version Plugin version.
     *      string $domain Site url.
     *      string|bool $endpoint Setka API server url.
     *      string|bool $basic_auth_login If Setka API server uses basic auth, then you can specify login.
     *      string|bool $basic_auth_password If Setka API server uses basic auth, then you can specify password.
     * }
     */
    protected $options;

    /**
     * API constructor.
     *
     * @param array $options Options which used by API.
     *
     * @see configureOptions
     */
    public function __construct(array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * @return AuthCredits
     */
    public function getAuthCredits()
    {
        return $this->authCredits;
    }

    /**
     * @param AuthCredits $authCredits
     *
     * @return $this
     */
    public function setAuthCredits(AuthCredits $authCredits)
    {
        $this->authCredits = $authCredits;
        return $this;
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param ValidatorInterface $validator
     *
     * @return $this For chain calls.
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * @return ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param ActionInterface $action
     *
     * @return $this For chain calls.
     */
    public function setAction(ActionInterface $action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param ClientInterface $client
     * @return $this For chain calls.
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return $this For chain calls.
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param OptionsResolver $resolver Empty resolver.
     * @return $this For chain calls.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('app_version')
            ->setAllowedTypes('app_version', 'string')

            ->setRequired('plugin_version')
            ->setAllowedTypes('plugin_version', 'string')

            ->setRequired('domain')
            ->setAllowedTypes('domain', 'string')

            ->setRequired('endpoint')
            ->setAllowedTypes('endpoint', 'string')
            ->setDefault('endpoint', Endpoints::API)

            ->setDefault('basic_auth_login', false)
            ->setAllowedTypes('basic_auth_login', array('bool', 'string'))

            ->setDefault('basic_auth_password', false)
            ->setAllowedTypes('basic_auth_password', array('bool', 'string'));

        return $this;
    }

    /**
     * Make API call based on passed $action.
     *
     * @param ActionInterface $action Action to perform.
     *
     * @return $this For chain calls.
     */
    public function request(ActionInterface $action)
    {
        $this->setAction($action);
        $action->setApi($this);

        if(!$action->getErrors()) {
            $action->setErrors(new ConstraintViolationList());
        }

        try {
            $action->configureAndResolveRequestDetails();
            $response =
                $this->getClient()
                    ->setUrl($this->getRequestUrl())
                    ->setDetails($this->getRequestDetails())
                    ->request()
                    ->getResult();
        } catch (\Exception $exception) {
            $error = new Errors\InvalidRequestDataError();
            $action->addError($error);
            return $this;
        }

        // Can't connect or something similar (error from Curl)
        if(is_wp_error($response)) {
            $action->addError(
                new Errors\ConnectionError(array(
                    'error' => $response,
                ))
            );
            return $this;
        }

        // Convert WordPress response object into Symfony Response object which is more useful
        try {
            $responseForAction = ResponseFactory::create($response);
            $responseForAction->parseContent();
        } catch (\Exception $exception) {
            $error = new Errors\ResponseError();
            $action->addError($error);
            return $this;
        }

        $action->setResponse($responseForAction);
        try {
            $action->handleResponse();
        } catch (\Exception $exception) {
            $action->addError(new ResponseError());
        }

        return $this;
    }

    /**
     * Returns an URL with desired parameters (query args-attrs) to make a request.
     *
     * I'm not using https://github.com/thephpleague/uri or http_build_url() because
     * they require additional libs in PHP such as ext-intl. This libs (additional dependencies)
     * not good for WordPress plugin.
     *
     * @return string Request URL.
     */
    public function getRequestUrl()
    {
        $url = $this->options['endpoint'];

        $endpoint = $this->getAction()->getEndpoint();
        $endpoint = ltrim($endpoint, '/');
        $endpoint = '/' . $endpoint;

        $url .= $endpoint;

        // Query args (attrs) like ?token=123&blahblah=123
        $url = add_query_arg($this->getRequestUrlQuery(), $url);

        return $url;
    }

    /**
     * @return array URL parameters merged with action parameters.
     */
    public function getRequestUrlQuery()
    {
        return array_merge_recursive(
            $this->getRequestUrlQueryRequired(),
            $this->getAction()->getRequestUrlQuery()
        );
    }

    /**
     * @return array URL parameters.
     */
    public function getRequestUrlQueryRequired()
    {
        return array(
            'app_version' => $this->options['app_version'],
            'domain'      => $this->options['domain'],
        );
    }

    /**
     * @return array Request details merged with action request details.
     */
    public function getRequestDetails()
    {
        return array_merge_recursive(
            $this->getRequestDetailsRequired(),
            $this->getAction()->getRequestDetails()
        );
    }

    /**
     * @return array Request details.
     */
    public function getRequestDetailsRequired()
    {
        $details =  array(
            'method' => $this->getAction()->getMethod(),
            'body'   => array(
                'plugin_version' => $this->options['plugin_version'],
            ),
        );

        if($this->getAction()->isAuthenticationRequired()) {
            $details['body']['token'] = $this->getAuthCredits()->getToken();
        }


        if($this->options['basic_auth_login'] && $this->options['basic_auth_password']) {
            $details['headers'] = array(
                'Authorization' => 'Basic ' . base64_encode($this->options['basic_auth_login'] . ':' . $this->options['basic_auth_password'])
            );
        }

        return $details;
    }
}

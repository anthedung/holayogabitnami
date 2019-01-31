<?php
namespace Setka\Editor\Admin\Service\SetkaAPI;

use Setka\Editor\Admin\Service\SetkaAPI\Errors\ResponseError;
use Setka\Editor\Admin\Service\SetkaAPI\Prototypes\ActionInterface;
use Setka\Editor\Plugin;
use Setka\Editor\Prototypes\Errors\Errors as ErrorsCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class API
{

    /**
     * @var \Setka\Editor\Admin\Service\SetkaAPI\AuthCredits
     */
    private $authCredits;

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * @var \Setka\Editor\Admin\Service\SetkaAPI\Prototypes\ActionInterface
     */
    private $action;

    /**
     * @var ClientInterface Interface to send HTTP requests
     */
    private $client;

    /**
     * @var array Options which API use while making requests.
     */
    protected $options;

    /**
     * API constructor.
     *
     * @param array $options
     *
     * @see configureOptions For understading which options required.
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('endpoint')
            ->setAllowedTypes('endpoint', 'string')

            ->setDefault('basic_auth_login', null)
            ->setAllowedTypes('basic_auth_login', array('null', 'string'))

            ->setDefault('basic_auth_password', null)
            ->setAllowedTypes('basic_auth_password', array('null', 'string'));

        return $this;
    }

    /**
     * Make API Call based on passed $action.
     *
     * @param ActionInterface $action
     *
     * @return $this For chain calls.
     */
    public function request(ActionInterface $action)
    {

        $this->setAction($action);
        $action->setApi($this);
        if(!$action->getErrors()) {
            $action->setErrors(new ErrorsCollection());
        }

        try {
            $action->configureAndResolveRequestDetails();
            $response = $this->getClient()
                             ->setUrl($this->getRequestUrl())
                             ->setDetails($this->getRequestDetails())
                             ->request()
                             ->getResult();
        } catch (\Exception $exception) {
            $error = new Errors\InvalidRequestDataError();
            $error->setData($exception);
            $action->getErrors()->add($error);
            return $this;
        }

        // Can't connect or something similar (error from CURL)
        if(is_wp_error($response)) {
            $action->getErrors()->add(
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
            $error->setData($exception);
            $action->getErrors()->add($error);
            return $this;
        }

        $action->setResponse($responseForAction);
        try {
            $action->handleResponse();
        } catch (\Exception $exception) {
            $action->getErrors()->add(new ResponseError());
        }

        return $this;
    }

    /**
     * Returns an URL with desired parameters (query args-attrs) to make a request.
     *
     * I'm not using https://github.com/thephpleague/uri or http_build_url() because
     * they require additional libs in PHP such as ext-intl. This libs (additional dependencies)
     * not good for WordPress plugin.
     */
    public function getRequestUrl()
    {
        $url = $this->options['endpoint'];

        $endpoint = $this->getAction()->getEndpoint();
        $endpoint = ltrim($endpoint, '/');
        $endpoint = '/' . $endpoint;

        $url .= $endpoint;

        // Query args (attrs) like ?token=123&blabla=123
        $url = add_query_arg($this->getRequestUrlQuery(), $url);

        return $url;
    }

    public function getRequestUrlQuery()
    {
        return array_merge_recursive(
            $this->getRequestUrlQueryRequired(),
            $this->getAction()->getRequestUrlQuery()
        );
    }

    public function getRequestUrlQueryRequired()
    {
        global $wp_version;
        return array(
            'app_version' => $wp_version,
            'domain'      => get_site_url(),
        );
    }

    public function getRequestDetails()
    {
        return array_merge_recursive(
            $this->getRequestDetailsRequired(),
            $this->getAction()->getRequestDetails()
        );
    }

    public function getRequestDetailsRequired()
    {
        $details =  array(
            'method' => $this->getAction()->getMethod(),
            'body'   => array(
                'plugin_version' => Plugin::VERSION,
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

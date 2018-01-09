<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Prototypes;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Prototypes\Errors\ErrorsInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class ActionAbstract implements ActionInterface {

    /**
     * @var string HTTP method.
     */
	protected $method;

    /**
     * @var string URL.
     */
	protected $endpoint;

	/**
	 * @var SetkaAPI\API
	 */
	protected $api;

	/**
	 * @var SetkaAPI\Response
	 */
	public $response;

	/**
	 * @var ErrorsInterface
	 */
	protected $errors;

	/**
	 * @var array The request details data
	 */
	protected $requestDetails = array();

	/**
	 * @var bool Flag which shows should we authenticate this request or not.
	 */
	protected $authenticationRequired = true;

    /**
     * @inheritdoc
     */
	public function getMethod() {
		return $this->method;
	}

    /**
     * @inheritdoc
     */
	public function setMethod($method) {
		$this->method = $method;
		return $this;
	}

    /**
     * @inheritdoc
     */
    public function getEndpoint() {
        return $this->endpoint;
    }

    /**
     * @inheritdoc
     */
    public function setEndpoint($endpoint) {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * @inheritdoc
     */
	public function getApi() {
		return $this->api;
	}

    /**
     * @inheritdoc
     */
	public function setApi(SetkaAPI\API $api) {
		$this->api = $api;
		return $this;
	}

    /**
     * @inheritdoc
     */
	public function getResponse() {
		return $this->response;
	}

    /**
     * @inheritdoc
     */
	public function setResponse(SetkaAPI\Response $response) {
		$this->response = $response;
		return $this;
	}

    /**
     * @inheritdoc
     */
	public function getErrors() {
		return $this->errors;
	}

    /**
     * @inheritdoc
     */
	public function setErrors(ErrorsInterface $errors) {
		$this->errors = $errors;
		return $this;
	}

    /**
     * @inheritdoc
     */
	public function getRequestUrlQuery() {
		return array();
	}

    /**
     * @inheritdoc
     */
	public function getRequestDetails() {
		return $this->requestDetails;
	}

    /**
     * @inheritdoc
     */
	public function setRequestDetails(array $requestDetails = array()) {
		$this->requestDetails = $requestDetails;
		return $this;
	}

    /**
     * @inheritdoc
     */
	public function configureAndResolveRequestDetails() {}

	/**
	 * @inheritdoc
	 */
	public function isAuthenticationRequired() {
		return $this->authenticationRequired;
	}

	/**
	 * @inheritdoc
	 */
	public function setAuthenticationRequired($authenticationRequired) {
		$this->authenticationRequired = (bool) $authenticationRequired;
		return $this;
	}

    /**
     * @param $violations ConstraintViolationListInterface
     * @throws \Exception If list have violations.
     * @return $this For chain calls.
     */
	public function violationsToException($violations) {
        if(count($violations) !== 0) {
            throw new \Exception();
        }
        return $this;
    }
}

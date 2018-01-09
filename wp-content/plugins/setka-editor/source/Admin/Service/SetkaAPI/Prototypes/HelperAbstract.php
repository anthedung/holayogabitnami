<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Prototypes;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Prototypes\Errors\ErrorsInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class HelperAbstract implements HelperInterface {

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
	 * @var Constraint[]
	 */
	protected $responseConstraints;

	/**
	 * HelperAbstract constructor.
     *
     * @param $api SetkaAPI\API
     * @param $response SetkaAPI\Response
     * @param $errors ErrorsInterface
	 */
	public function __construct(SetkaAPI\API $api, SetkaAPI\Response $response, ErrorsInterface $errors) {
		$this
            ->setApi($api)
            ->setResponse($response)
		    ->setErrors($errors);
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
	public function getResponseConstraints() {
		if(!$this->responseConstraints) {
			$this->responseConstraints = $this->buildResponseConstraints();
		}
		return $this->responseConstraints;
	}

    /**
     * @inheritdoc
     */
	public function setResponseConstraints($constraints) {
		$this->responseConstraints = $constraints;
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

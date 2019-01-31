<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI\Prototypes;

use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class HelperAbstract implements HelperInterface
{
    /**
     * @var SetkaEditorAPI\API
     */
    protected $api;

    /**
     * @var SetkaEditorAPI\Response
     */
    public $response;

    /**
     * @var ConstraintViolationListInterface
     */
    protected $errors;

    /**
     * @var Constraint[]
     */
    protected $responseConstraints;

    /**
     * HelperAbstract constructor.
     *
     * @param $api SetkaEditorAPI\API
     * @param $response SetkaEditorAPI\Response
     * @param $errors ConstraintViolationListInterface
     */
    public function __construct(SetkaEditorAPI\API $api, SetkaEditorAPI\Response $response, ConstraintViolationListInterface $errors)
    {
        $this
            ->setApi($api)
            ->setResponse($response)
            ->setErrors($errors);
    }

    /**
     * @inheritdoc
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @inheritdoc
     */
    public function setApi(SetkaEditorAPI\API $api)
    {
        $this->api = $api;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @inheritdoc
     */
    public function setResponse(SetkaEditorAPI\Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @inheritdoc
     */
    public function setErrors(ConstraintViolationListInterface $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addError(ConstraintViolationInterface $error)
    {
        $this->errors->add($error);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getResponseConstraints()
    {
        if (!$this->responseConstraints) {
            $this->responseConstraints = $this->buildResponseConstraints();
        }
        return $this->responseConstraints;
    }

    /**
     * @inheritdoc
     */
    public function setResponseConstraints($constraints)
    {
        $this->responseConstraints = $constraints;
        return $this;
    }

    /**
     * @param $violations ConstraintViolationListInterface
     * @throws \Exception If list have violations.
     * @return $this For chain calls.
     */
    public function violationsToException($violations)
    {
        if (count($violations) !== 0) {
            throw new \Exception();
        }
        return $this;
    }
}

<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI\Prototypes;

use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Interface HelperInterface
 */
interface HelperInterface
{
    /**
     * @return SetkaEditorAPI\API
     */
    public function getApi();

    /**
     * @param SetkaEditorAPI\API $api
     *
     * @return $this For chain calls.
     */
    public function setApi(SetkaEditorAPI\API $api);

    /**
     * @return SetkaEditorAPI\Response
     */
    public function getResponse();

    /**
     * @param SetkaEditorAPI\Response $response
     *
     * @return $this For chain calls.
     */
    public function setResponse(SetkaEditorAPI\Response $response);

    /**
     * @return ConstraintViolationListInterface
     */
    public function getErrors();

    /**
     * @param ConstraintViolationListInterface $errors
     *
     * @return $this For chain calls.
     */
    public function setErrors(ConstraintViolationListInterface $errors);

    /**
     * Add single error to errors list.
     *
     * @param ConstraintViolationInterface $error Error to add.
     * @return $this For chain calls.
     */
    public function addError(ConstraintViolationInterface $error);

    /**
     * @return Constraint|Constraint[]
     */
    public function getResponseConstraints();

    /**
     * @param $constraints Constraint|Constraint[]
     * @return $this For chain calls.
     */
    public function setResponseConstraints($constraints);

    /**
     * @return Constraint|Constraint[]
     */
    public function buildResponseConstraints();

    /**
     * Validates the response from server and adds errors.
     *
     * @return $this For chain calls.
     */
    public function handleResponse();
}

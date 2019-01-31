<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI\Prototypes;

use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Interface ActionInterface
 */
interface ActionInterface
{
    /**
     * @return string HTTP method.
     */
    public function getMethod();

    /**
     * @param $method string HTTP method.
     *
     * @return $this For chain calls.
     */
    public function setMethod($method);

    /**
     * @return string Path in API.
     */
    public function getEndpoint();

    /**
     * @param $endpoint string Path in API.
     *
     * @return $this For chain calls.
     */
    public function setEndpoint($endpoint);

    /**
     * @return SetkaEditorAPI\API Setka Editor API.
     */
    public function getApi();

    /**
     * @param SetkaEditorAPI\API $api Setka Editor API.
     *
     * @return $this For chain calls.
     */
    public function setApi(SetkaEditorAPI\API $api);

    /**
     * @return SetkaEditorAPI\Response HTTP Response object.
     */
    public function getResponse();

    /**
     * @param SetkaEditorAPI\Response $response HTTP Response object.
     *
     * @return $this For chain calls.
     */
    public function setResponse(SetkaEditorAPI\Response $response);

    /**
     * @return ConstraintViolationListInterface Error in validating Response.
     */
    public function getErrors();

    /**
     * @param ConstraintViolationListInterface $errors Error in validating Response.
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
     * @return array Array with arguments for the request query.
     */
    public function getRequestUrlQuery();

    /**
     * @return array
     */
    public function getRequestDetails();

    /**
     * @param $requestDetails array Data for request.
     *
     * @return $this For chain calls.
     */
    public function setRequestDetails(array $requestDetails = array());

    /**
     * Builds the OptionsResolver instance.
     *
     * @return $this For chain calls.
     */
    public function configureAndResolveRequestDetails();

    /**
     * Validates response from server and add errors.
     *
     * @return $this For chain calls.
     */
    public function handleResponse();

    /**
     * @return bool True if request needs AuthCredits.
     */
    public function isAuthenticationRequired();

    /**
     * @param $authenticationRequired bool True if request requires AuthCredits.
     *
     * @return $this For chain calls.
     */
    public function setAuthenticationRequired($authenticationRequired);
}

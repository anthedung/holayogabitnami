<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Prototypes;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Prototypes\Errors\ErrorsInterface;

interface ActionInterface
{

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param $method string Name of HTTP method
     *
     * @return $this
     */
    public function setMethod($method);

    /**
     * @return string
     */
    public function getEndpoint();

    /**
     * @param $endpoint string
     *
     * @return $this
     */
    public function setEndpoint($endpoint);

    /**
     * @return SetkaAPI\API
     */
    public function getApi();

    /**
     * @param SetkaAPI\API $api
     *
     * @return $this
     */
    public function setApi(SetkaAPI\API $api);

    /**
     * @return SetkaAPI\Response
     */
    public function getResponse();

    /**
     * @param SetkaAPI\Response $response
     *
     * @return $this
     */
    public function setResponse(SetkaAPI\Response $response);

    /**
     * @return ErrorsInterface
     */
    public function getErrors();

    /**
     * @param ErrorsInterface $errors
     *
     * @return $this
     */
    public function setErrors(ErrorsInterface $errors);

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
     * @return $this
     */
    public function configureAndResolveRequestDetails();

    public function getConstraint();

    /**
     * @return null Validates the response from server and adds errors (if needed).
     */
    public function handleResponse();

    /**
     * @return bool
     */
    public function isAuthenticationRequired();

    /**
     * @param $authenticationRequired bool
     *
     * @return $this
     */
    public function setAuthenticationRequired($authenticationRequired);
}

<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Prototypes;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Prototypes\Errors\ErrorsInterface;

interface HelperInterface {

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

	public function getResponseConstraints();

	public function setResponseConstraints($constraints);

	public function buildResponseConstraints();

    /**
     * @return null Validates the response from server and adds errors (if needed).
     */
	public function handleResponse();
}

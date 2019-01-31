<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI\Actions;

use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Setka\Editor\Admin\Service\SetkaEditorAPI\Errors;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UpdateStatusAction
 */
class UpdateStatusAction extends SetkaEditorAPI\Prototypes\ActionAbstract
{
    /**
     * UpdateStatusAction constructor.
     */
    public function __construct()
    {
        $this
            ->setMethod(Request::METHOD_POST)
            ->setEndpoint('/api/v1/wordpress/setup_statuses/update_status.json');
    }

    /**
     * @inheritdoc
     */
    public function handleResponse()
    {
        $response = $this->getResponse();

        switch($response->getStatusCode()) {
            case $response::HTTP_OK:
            case $response::HTTP_CREATED:
            case $response::HTTP_ACCEPTED:
            case $response::HTTP_NON_AUTHORITATIVE_INFORMATION:
            case $response::HTTP_NO_CONTENT:
            case $response::HTTP_RESET_CONTENT:
            case $response::HTTP_PARTIAL_CONTENT:
            case $response::HTTP_MULTI_STATUS:
            case $response::HTTP_ALREADY_REPORTED:
            case $response::HTTP_IM_USED:
                // For now we don't check anything because we don't use this data
                break;

            case $response::HTTP_UNPROCESSABLE_ENTITY: // Wrong `status` field in request.
                $this->getErrors()->add(new Errors\InvalidRequestDataError());
                break;

            default:
                $this->getErrors()->add(new Errors\UnknownError());
                break;
        }

        return $this;
    }
}

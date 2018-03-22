<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Actions;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Service\SetkaAPI\Errors;
use Symfony\Component\HttpFoundation\Request;

class ResendTokenLetterAction extends SetkaAPI\Prototypes\ActionAbstract
{

    public function __construct()
    {
        $this
            ->setAuthenticationRequired(false)
            ->setMethod(Request::METHOD_POST)
            ->setEndpoint('/api/v1/wordpress/signups/resend_token_letter');
    }

    public function getConstraint()
    {
    }

    public function handleResponse()
    {
        $response = $this->getResponse();
        $errors   = $this->getErrors();

        switch($response->getStatusCode()) {
            // 200
            case $response::HTTP_OK:
                break;

            // 404
            case $response::HTTP_NOT_FOUND:
                $helper = new SetkaAPI\Helpers\ErrorHelper($this->getApi(), $response, $errors);
                $helper->handleResponse();
                break;

            default:
                $errors->add(new Errors\UnknownError());
                break;
        }
    }
}

<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Actions;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Service\SetkaAPI\Errors;
use Setka\Editor\Admin\Service\SetkaAPI\Helpers;
use Symfony\Component\HttpFoundation\Request;

class GetFilesAction extends SetkaAPI\Prototypes\ActionAbstract
{

    /**
     * GetFilesAction constructor.
     */
    public function __construct()
    {
        $this
            ->setMethod(Request::METHOD_GET)
            ->setEndpoint('/api/v1/wordpress/files.json');
    }

    public function getConstraint()
    {
    }

    public function handleResponse()
    {
        $response = $this->getResponse();

        switch($response->getStatusCode()) {
            case $response::HTTP_OK:
                break;

            default:
                $this->getErrors()->add(new Errors\UnknownError());
                return;
        }

        $helper = new Helpers\FileHelper($this->getApi(), $response, $this->getErrors());
        $helper->handleResponse();
    }
}

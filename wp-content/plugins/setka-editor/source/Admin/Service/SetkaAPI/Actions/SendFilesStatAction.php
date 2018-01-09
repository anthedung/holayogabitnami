<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Actions;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Service\SetkaAPI\Errors;
use Symfony\Component\HttpFoundation\Request;

class SendFilesStatAction extends SetkaAPI\Prototypes\ActionAbstract {

    /**
     * SendFilesStatAction constructor.
     */
    public function __construct() {
        $this
            ->setMethod(Request::METHOD_POST)
            ->setEndpoint('/api/v1/wordpress/files/event.json');
    }

    public function getConstraint() {}

    public function handleResponse() {
        $response = $this->getResponse();

        switch($response->getStatusCode()) {
            // 200 // OK
            case $response::HTTP_OK:
                // Check for valid response content
                // for now we don't check anything because we don't use this data
                break;

            default:
                $this->getErrors()->add(new Errors\UnknownError());
                break;
        }
    }
}

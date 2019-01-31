<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI\Actions;

use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Setka\Editor\Admin\Service\SetkaEditorAPI\Errors;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SendFilesStatAction
 */
class SendFilesStatAction extends SetkaEditorAPI\Prototypes\ActionAbstract
{
    /**
     * SendFilesStatAction constructor.
     */
    public function __construct()
    {
        $this
            ->setMethod(Request::METHOD_POST)
            ->setEndpoint('/api/v1/wordpress/files/event.json');
    }

    /**
     * @inheritdoc
     */
    public function handleResponse()
    {
        $response = $this->getResponse();

        switch($response->getStatusCode()) {
            case $response::HTTP_OK:
                // For now we don't check anything because we don't use this data in plugin.
                break;

            default:
                $this->addError(new Errors\UnknownError());
                break;
        }

        return $this;
    }
}

<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Actions;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Service\SetkaAPI\Errors;
use Setka\Editor\Admin\Service\SetkaAPI\Helpers;
use Symfony\Component\HttpFoundation;
use Setka\Editor\Admin\Options;

class GetCurrentThemeAnonymouslyAction extends SetkaAPI\Prototypes\ActionAbstract
{
    public function __construct()
    {
        $this
            ->setMethod(HttpFoundation\Request::METHOD_GET)
            ->setEndpoint('/api/v1/wordpress/default_files.json')
            ->setAuthenticationRequired(false);
    }

    public function getConstraint()
    {
    }

    public function handleResponse()
    {
        $response = $this->getResponse();
        $errors   = $this->getErrors();

        switch($response->getStatusCode()) {
            /**
             * // 200 //
             * theme_files and content_editor_files must presented in response
             */
            case $response::HTTP_OK:
                $helper = new Helpers\ContentEditorFilesHelper($this->getApi(), $response, $errors);
                $helper->handleResponse();
                if($errors->hasErrors()) {
                    return;
                }

                $helper = new Helpers\ThemeFilesHelper($this->getApi(), $response, $errors);
                $helper->handleResponse();
                if($errors->hasErrors()) {
                    return;
                }

                $helper = new Helpers\PluginsHelper($this->getApi(), $response, $errors);
                $helper->handleResponse();
                if($errors->hasErrors()) {
                    return;
                }

                break;

            // 401 // Token not found
            case $response::HTTP_UNAUTHORIZED:
                $errors->add(new Errors\ServerUnauthorizedError());
                break;

            default:
                $errors->add(new Errors\UnknownError());
                break;
        }
    }
}

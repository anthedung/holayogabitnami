<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Actions;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Service\SetkaAPI\Errors;
use Setka\Editor\Admin\Service\SetkaAPI\Helpers;
use Symfony\Component\HttpFoundation;
use Setka\Editor\Admin\Options;

class GetCurrentThemeAction extends SetkaAPI\Prototypes\ActionAbstract {

    /**
     * GetCurrentThemeAction constructor.
     */
    public function __construct() {
        $this
            ->setMethod(HttpFoundation\Request::METHOD_POST)
            ->setEndpoint('/api/v1/wordpress/current_theme.json');
    }

    public function getConstraint() {}

	public function handleResponse() {
		$response = $this->getResponse();
		$errors = $this->getErrors();

		switch($response->getStatusCode()) {
			/**
			 * // 200 //
			 * theme_files and content_editor_files must presented in response
			 */
			case $response::HTTP_OK:

                /**
                 * @var $content HttpFoundation\ParameterBag
                 */
                $content = $response->getContent();

				// Content Editor Files
				$helper = new Helpers\ContentEditorFilesHelper($this->getApi(), $response, $errors);
				$helper->handleResponse();
				if($errors->hasErrors())
					return;

				// Theme Files
				$helper = new Helpers\ThemeFilesHelper($this->getApi(), $response, $errors);
				$helper->handleResponse();
				if($errors->hasErrors())
					return;

				// Plugin Files
				$helper = new Helpers\PluginsHelper($this->getApi(), $response, $errors);
				$helper->handleResponse();
				if($errors->hasErrors())
					return;

                // Public Token
                $publicTokenOption = new Options\PublicToken\PublicTokenOption();
                try {
                    $results = $publicTokenOption->validateValue($content->get('public_token'));
                    $this->violationsToException($results);
                } catch (\Exception $exception) {
                    $this->getErrors()->add(new Errors\ResponseBodyInvalidError());
                    return;
                }

				return;

			// 401 // Token not found
			case $response::HTTP_UNAUTHORIZED:
                $errors->add(new Errors\ServerUnauthorizedError());
				return;

			/**
			 * // 403 //
			 * This status code means what subscription is canceled or something.
			 * But in this case API also response with valid theme_files.
			 * Creating new posts functionality disabled but old posts
			 * can correctly displayed.
			 */
			case $response::HTTP_FORBIDDEN:

				// Errors
				$helper = new Helpers\ErrorHelper($this->getApi(), $response, $errors);
				$helper->handleResponse();
				if($errors->hasErrors())
					return;

				// Theme Files
				$helper = new Helpers\ThemeFilesHelper($this->getApi(), $response, $errors);
				$helper->handleResponse();
				if($errors->hasErrors())
					return;

				return;

			default:
                $errors->add(new Errors\UnknownError());
		}
	}
}

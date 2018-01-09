<?php
namespace Setka\Editor\API\V1\Actions\Files;

use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;
use Setka\Editor\API\V1\Prototypes\AbstractAction;
use Setka\Editor\API\V1\Helpers;
use Setka\Editor\API\V1\Errors;
use Setka\Editor\API\V1;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints;

class UpdateFilesAction extends AbstractAction {

	public function __construct(V1\API $api) {
		parent::__construct($api);
		$this->setEndpoint('files/update');
	}

	public function handleRequest() {
		$request  = $this->getRequest();
		$response = $this->getResponse();
		$api      = $this->getApi();

        $response->setStatusCode($response::HTTP_BAD_REQUEST);

		// Only POST requests allowed in this action.
		if($request->getMethod() !== $request::METHOD_POST) {
			$api->addError(new Errors\HttpMethodError());
			$response->setStatusCode($response::HTTP_BAD_REQUEST);
			return;
		}

		// We need a [data] and token key here
		if(!$request->request->has('data')) {
			$api->addError(new Errors\MissedDataAttributeError());
            $response->setStatusCode($response::HTTP_BAD_REQUEST);
			return;
		}

		// Convert data from array to ParameterBag if not
		if(is_array($request->request->get('data'))) {
			$request->request->set(
				'data',
				new ParameterBag($request->request->get('data'))
			);
		}

		if(!is_a($request->request->get('data'), ParameterBag::class)) {
			$api->addError(new Errors\RequestDataError());
            $response->setStatusCode($response::HTTP_BAD_REQUEST);
			return;
		}

		// Validate token
		$tokenAction = new Helpers\Auth\Helper($this->getApi());
		$tokenAction->handleRequest();
		// Token not valid
		if(!$response->isOk()) {
			return;
		}

		$data = $request->request->get('data');

		// Validate files.
		if(!$data->has('files')) {
			$api->addError(new Errors\Files\FilesError());
            $response->setStatusCode($response::HTTP_BAD_REQUEST);
			return;
		}

		$files =& $data->get('files');

		if(!is_array($files)) {
			$api->addError(new Errors\Files\FilesError());
            $response->setStatusCode($response::HTTP_BAD_REQUEST);
			return;
		}

		$constraints = $this->getConstraint();
		$validator = $api->getValidator();

		foreach($files as &$file) {
			if(!is_array($file)) {
				$api->addError(new Errors\Files\FilesError());
                $response->setStatusCode($response::HTTP_BAD_REQUEST);
				return;
			} else {
				$result = $validator->validate($file, $constraints);

				if(count($result) !== 0) {
					$error = new Errors\Files\FilesError();
					$error->setData($file);
					$api->addError($error);
                    $response->setStatusCode($response::HTTP_BAD_REQUEST);
					return;
				}
			}
		}

		unset($file, $result);

		// Save files
		$filesOption = new Options\Files\FilesOption();
		$filesOption->updateValue($files);

        // Reset download attempts counters
        $this->resetAllDownloadsCounters();
        if($api->getResponseErrors()->hasErrors()) {
            $response->setStatusCode($response::HTTP_BAD_REQUEST);
            return;
        }

		$response->setStatusCode($response::HTTP_OK);
	}

	protected function resetAllDownloadsCounters() {
		try {
			$manager = FilesManagerFactory::create();
			$manager
                ->resetAllDownloadsCounters()
                ->restartSyncing();
		} catch (\Exception $exception) {
			$error = new Errors\SiteError();
			$error->setData($exception);
			$this->getApi()->addError($error);
		}
		return $this;
	}

	public function getConstraint() {
		return new Constraints\Collection(array(
			'fields' => array(
				'id' => array(
					new Constraints\NotBlank(),
					new Constraints\Type('int'),
					new Constraints\GreaterThan(0),
				),
				'url' => array(
					new Constraints\NotBlank(),
					new Constraints\Url(),
				),
				'filetype' => array(
					new Constraints\NotBlank(),
					new Constraints\Type('string'),
				),
			),
			'allowExtraFields' => true,
		));
	}
}

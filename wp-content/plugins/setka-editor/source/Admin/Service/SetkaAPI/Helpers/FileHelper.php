<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Helpers;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Service\SetkaAPI\Errors;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints;

class FileHelper extends SetkaAPI\Prototypes\HelperAbstract {

	public function buildResponseConstraints() {
		return array(
            new Constraints\NotBlank(),
		    new Constraints\Collection(array(
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
		    ))
        );
	}

	public function handleResponse() {
		$response = $this->getResponse();
		$content = $response->getContent();

		if(!is_a($content, ParameterBag::class)) {
			$this->getErrors()->add(new Errors\ResponseBodyInvalidError());
			return;
		}

		$validator = $this->getApi()->getValidator();
		$constraints = $this->buildResponseConstraints();

		// Validate each file.
		foreach($content as $file) {
			try {
                $results = $validator->validate($file, $constraints);
			    $this->violationsToException($results);
            } catch (\Exception $exception) {
				$this->getErrors()->add(new Errors\GetFiles\InvalidFileError());
				return;
            }
		}
	}
}

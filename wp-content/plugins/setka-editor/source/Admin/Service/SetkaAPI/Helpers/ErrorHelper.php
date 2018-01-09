<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Helpers;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Service\SetkaAPI\Errors;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints;

class ErrorHelper extends SetkaAPI\Prototypes\HelperAbstract {

	public function buildResponseConstraints() {
		return array(
			new Constraints\NotBlank(),
			new Constraints\Type(array(
				'type' => 'string',
			)),
		);
	}

	public function handleResponse() {
		$response = $this->getResponse();
		$content = $response->getContent();

		// Single error message from API
		if($content->has('error') && !$content->has('errors')) {
            $validator = $this->getApi()->getValidator();
			try {
                $this->violationsToException(
                    $validator->validate(
                        $content->get('error'),
                        $this->getResponseConstraints()
                    )
                );
            } catch (\Exception $exception) {
                $this->getErrors()->add( new Errors\ResponseBodyInvalidError() );
                return;
            }

            // One valid error.
            return;
		}
		// Multiple error messages from API
		elseif($content->has('errors') && !$content->has('error')) {
			$this->validateErrors($content->get('errors'));
		} else {
			$this->getErrors()->add(new Errors\ResponseBodyInvalidError());
		}
	}

	/**
	 * Validates the errors list.
	 *
	 * @param $errors array A list of errors.
	 */
	protected function validateErrors($errors) {
		/**
		 * An example of errors list.
		 * $a = array(
			'errors' => array(
				'email' => array(
					0 => '123',
					1 => '234',
				),
				'first_name' => array(
					0 => '123',
					1 => '456',
				),
			),
		);*/

		if($this->getErrors()->hasErrors())
		    return;

		if(is_array($errors)) {
			// Validate each error in set
			$validator = $this->getApi()->getValidator();
			$constraint = $this->getResponseConstraints();

			foreach($errors as $error_key => &$error_value) {
				if(is_array($error_value)) {
					$this->validateErrors($error_value);
					return;
				} else {
				    try {
                        $results = $validator->validate($error_value, $constraint);
                        $this->violationsToException($results);
                    } catch (\Exception $exception) {
                        $this->getErrors()->add(new Errors\ResponseBodyInvalidError());
                        return;
                    }
				}
			}
		} else {
			$this->getErrors()->add(new Errors\ResponseBodyInvalidError());
		}
	}
}

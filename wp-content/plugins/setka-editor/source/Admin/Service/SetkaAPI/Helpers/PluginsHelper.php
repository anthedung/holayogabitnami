<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Helpers;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Service\SetkaAPI\Errors;
use Symfony\Component\Validator\Constraints;

class PluginsHelper extends SetkaAPI\Prototypes\HelperAbstract {

	public function buildResponseConstraints() {
		return array(
			new Constraints\NotBlank(),
			new Constraints\Collection(array(
				'fields' => array(
					'url' => array(
						new Constraints\NotBlank(),
						new Constraints\Url(),
					),
					'filetype' => array(
						new Constraints\NotBlank(),
						new Constraints\IdenticalTo(array(
							'value' => 'js',
						)),
					),
				),
				'allowExtraFields' => true,
			)),
		);
	}

	public function handleResponse() {
		$response = $this->getResponse();

		try {
            $plugins = $response->getContent()->get('plugins');

            if(!isset($plugins[0]))
                throw new \Exception();

            $validator = $this->getApi()->getValidator();
            $constraints = $this->getResponseConstraints();

            $this->violationsToException(
                $validator->validate($plugins[0], $constraints));
        } catch (\Exception $exception) {
            $this->getErrors()->add(new Errors\ResponseBodyInvalidError());
            return;
        }
	}
}

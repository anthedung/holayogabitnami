<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Helpers;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Service\SetkaAPI\Errors;
use Symfony\Component\Validator\Constraints;

class ThemeFilesHelper extends SetkaAPI\Prototypes\HelperAbstract
{

    public function buildResponseConstraints()
    {
        return array(
            new Constraints\NotBlank(),
            new Constraints\Collection(array(
                'fields' => array(
                    'id' => array(
                        new Constraints\NotBlank(),
                        new Constraints\Type(array(
                            'type' => 'numeric',
                        )),
                    ),
                    'url' => array(
                        new Constraints\NotBlank(),
                        new Constraints\Url(),
                    ),
                    'filetype' => array(
                        new Constraints\NotBlank(),
                        new Constraints\Choice(array(
                            'choices' => array('css', 'js', 'svg', 'json'),
                            'strict' => true,
                        )),
                    ),
                ),
                'allowExtraFields' => true,
            )),
        );
    }

    public function handleResponse()
    {
        $response = $this->getResponse();
        $content  = $response->getContent();

        if(!is_array($content->get('theme_files'))) {
            $this->getErrors()->add(new Errors\ResponseBodyInvalidError());
            return;
        }

        $validator   = $this->getApi()->getValidator();
        $constraints = $this->getResponseConstraints();

        $css = $json = false;
        foreach($content->get('theme_files') as $file) {
            try {
                $results = $validator->validate($file, $constraints);
                $this->violationsToException($results);
            } catch (\Exception $exception) {
                $this->getErrors()->add(new Errors\ResponseBodyInvalidError());
                return;
            }

            switch($file['filetype']) {
                case 'css':
                    $css = true;
                    break;
                case 'json':
                    $json = true;
                    break;
                default:
                    break;
            }
        }

        if($css && $json) {
            return;
        }

        $this->getErrors()->add(new Errors\ResponseBodyInvalidError());
    }
}

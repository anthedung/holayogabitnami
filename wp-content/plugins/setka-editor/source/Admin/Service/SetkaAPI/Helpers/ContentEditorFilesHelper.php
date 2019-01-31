<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Helpers;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Service\SetkaAPI\Errors;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints;
use Setka\Editor\Admin\Options\EditorVersion\Option as EditorVersion;

class ContentEditorFilesHelper extends SetkaAPI\Prototypes\HelperAbstract
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
                            'choices' => array('css', 'js'),
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
        $response  = $this->getResponse();
        $validator = $this->getApi()->getValidator();
        $errors    = $this->getErrors();
        $content   = $response->getContent();

        try {
            $editorVersion = new EditorVersion();
            $results       = $validator->validate(
                $content->get('content_editor_version'),
                $editorVersion->getConstraint()
            );
            $this->violationsToException($results);
        } catch (\Exception $exception) {
            $errors->add(new Errors\ResponseBodyInvalidError());
            return;
        }
        unset($editorVersion, $results);

        $editorFiles = $content->get('content_editor_files');
        if(!is_array($editorFiles) || empty($editorFiles)) {
            $errors->add(new Errors\ResponseBodyInvalidError());
            return;
        }

        $css         = $js = false;
        $constraints = $this->getResponseConstraints();
        foreach($editorFiles as $file) {
            try {
                $results = $validator->validate($file, $constraints);
                $this->violationsToException($results);
            } catch (\Exception $exception) {
                $errors->add(new Errors\ResponseBodyInvalidError());
                return;
            }
            switch($file['filetype']) {
                case 'css':
                    $css = true;
                    break;
                case 'js':
                    $js = true;
                    break;
                default:
                    break;
            }
        }

        if($css && $js) {
            return;
        }

        $this->getErrors()->add(new Errors\ResponseBodyInvalidError());
    }
}

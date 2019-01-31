<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI\Helpers;

use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Setka\Editor\Admin\Service\SetkaEditorAPI\Errors;
use Symfony\Component\Validator\Constraints;

/**
 * Class ErrorHelper
 */
class ErrorHelper extends SetkaEditorAPI\Prototypes\HelperAbstract
{
    /**
     * @inheritdoc
     */
    public function buildResponseConstraints()
    {
        return array(
            new Constraints\NotBlank(),
            new Constraints\All(array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\All(array(
                        'constraints' => array(
                            new Constraints\NotBlank(),
                            new Constraints\Type(array(
                                'type' => 'string',
                            )),
                        ),
                    )),
                ),
            )),
        );
    }
    
    /**
     * @inheritdoc
     */
    public function handleResponse()
    {
        $response = $this->getResponse();
        $content  = $response->getContent();

        if($content->has('error') && !$content->has('errors')) { // Single error message from API
            $errors = array(
                '1' => array(
                    $content->get('error')
                )
            );
        } elseif($content->has('errors') && !$content->has('error')) { // Multiple error messages from API
            $errors = $content->get('errors');
        } else {
            $this->getErrors()->add(new Errors\ResponseBodyInvalidError());
            return $this;
        }

        try {
            $results = $this->getApi()->getValidator()->validate(
                $errors,
                $this->buildResponseConstraints()
            );
            $this->getErrors()->addAll($results);
        } catch (\Exception $exception) {
            $this->addError(new Errors\ResponseBodyInvalidError());
        }

        return $this;
    }
}

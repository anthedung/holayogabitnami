<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI\Actions;

use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Setka\Editor\Admin\Service\SetkaEditorAPI\Errors;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

/**
 * Class GetFilesAction
 */
class GetFilesAction extends SetkaEditorAPI\Prototypes\ActionAbstract
{
    /**
     * GetFilesAction constructor.
     */
    public function __construct()
    {
        $this
            ->setMethod(Request::METHOD_GET)
            ->setEndpoint('/api/v1/wordpress/files.json');
    }

    /**
     * @inheritdoc
     */
    public function handleResponse()
    {
        $response = $this->getResponse();

        switch($response->getStatusCode()) {
            case $response::HTTP_OK:
                $this->validateOk($response->content);
                break;

            default:
                $this->getErrors()->add(new Errors\UnknownError());
                break;
        }

        return $this;
    }

    /**
     * Validates HTTP 200 data.
     *
     * @param ParameterBag $content Parameters to validate.
     * @return $this For chain calls.
     */
    public function validateOk(ParameterBag $content)
    {
        try {
            $results = $this->getApi()->getValidator()->validate(
                $content->all(),
                $this->buildConstraintsOk()
            );
            $this->getErrors()->addAll($results);
        } catch (\Exception $exception) {
            $this->addError(new Errors\ResponseBodyInvalidError());
        }
        return $this;
    }

    /**
     * @return Constraint[]
     */
    public function buildConstraintsOk()
    {
        $constraint = array(
            new Constraints\NotBlank(),
            new Constraints\All(array(
                'constraints' => array(
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
                                new Constraints\Type('string'),
                            ),
                        ),
                        'allowExtraFields' => true,
                    )),
                ),
            )),
        );

        return $constraint;
    }
}

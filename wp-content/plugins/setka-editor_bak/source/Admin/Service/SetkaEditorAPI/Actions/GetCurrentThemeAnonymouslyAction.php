<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI\Actions;

use Setka\Editor\Admin\Options\EditorVersionOption;
use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Setka\Editor\Admin\Service\SetkaEditorAPI\Errors;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

/**
 * Class GetCurrentThemeAnonymouslyAction
 */
class GetCurrentThemeAnonymouslyAction extends SetkaEditorAPI\Prototypes\ActionAbstract
{
    public function __construct()
    {
        $this
            ->setMethod(Request::METHOD_GET)
            ->setEndpoint('/api/v1/wordpress/default_files.json')
            ->setAuthenticationRequired(false);
    }

    /**
     * @inheritdoc
     */
    public function handleResponse()
    {
        $response = $this->getResponse();
        $errors   = $this->getErrors();

        switch($response->getStatusCode()) {
            case $response::HTTP_OK:
                $this->validateOk($response->content);
                break;

            case $response::HTTP_UNAUTHORIZED: // Token not found
                $errors->add(new Errors\ServerUnauthorizedError());
                break;

            default:
                $errors->add(new Errors\UnknownError());
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
     * @return Constraint
     */
    public function buildConstraintsOk()
    {
        $editorVersionOption = new EditorVersionOption();

        $constraint = new Constraints\Collection(array(
            'fields' => array(


                'content_editor_version' => new Constraints\Required($editorVersionOption->buildConstraint()),


                'content_editor_files' => array(
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
                                        new Constraints\Choice(array(
                                            'choices' => array('css', 'js'),
                                            'strict' => true,
                                        )),
                                    ),
                                ),
                                'allowExtraFields' => true,
                            )),
                        ),
                    )),
                    new Constraints\Callback(array(new SetkaEditorAPI\CheckAllFilesExists(array('css', 'js')), 'validate')),
                ),


                'theme_files' => array(
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
                                        new Constraints\Choice(array(
                                            'choices' => array('css', 'js', 'svg', 'json'),
                                            'strict' => true,
                                        )),
                                    ),
                                ),
                                'allowExtraFields' => true,
                            )),
                        ),
                    )),
                    new Constraints\Callback(array(new SetkaEditorAPI\CheckAllFilesExists(array('css', 'json')), 'validate')),
                ),


                'plugins' => array(
                    new Constraints\NotBlank(),
                    new Constraints\All(array(
                        'constraints' => array(
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
                        ),
                    )),
                    new Constraints\Callback(array(new SetkaEditorAPI\CheckAllFilesExists(array('js')), 'validate')),
                ),
            ),
            'allowExtraFields' => true,
        ));

        return $constraint;
    }
}

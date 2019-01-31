<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI\Actions;

use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Setka\Editor\Admin\Service\SetkaEditorAPI\Errors;
use Setka\Editor\Admin\Service\SetkaEditorAPI\Helpers;
use Symfony\Component\HttpFoundation\ParameterBag;
use Setka\Editor\Admin\Options;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;

/**
 * Class GetCurrentThemeAction
 */
class GetCurrentThemeAction extends SetkaEditorAPI\Prototypes\ActionAbstract
{
    /**
     * GetCurrentThemeAction constructor.
     */
    public function __construct()
    {
        $this
            ->setMethod(Request::METHOD_POST)
            ->setEndpoint('/api/v1/wordpress/current_theme.json');
    }

    /**
     * @inheritdoc
     */
    public function handleResponse()
    {
        $response = $this->getResponse();
        $errors   = $this->getErrors();

        switch($response->getStatusCode()) {
            case $response::HTTP_OK: // theme_files and content_editor_files must presented in response
                $this->validateOk($response->content);
                break;

            case $response::HTTP_UNAUTHORIZED: // Token not found
                $this->addError(new Errors\ServerUnauthorizedError());
                break;

            /**
             * This status code means what subscription is canceled.
             * But in this case API also response with valid theme_files.
             * Creating new posts functionality disabled but old posts
             * can correctly displayed.
             */
            case $response::HTTP_FORBIDDEN:
                $helper = new Helpers\ErrorHelper($this->getApi(), $response, $errors);
                $helper->handleResponse();

                $this->validateForbidden($response->content);
                break;

            default:
                $this->addError(new Errors\UnknownError());
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
     * Validates HTTP 403 data.
     *
     * @param ParameterBag $content Parameters to validate.
     * @return $this For chain calls.
     */
    public function validateForbidden(ParameterBag $content)
    {
        try {
            $results = $this->getApi()->getValidator()->validate(
                $content->all(),
                $this->buildConstraintsForbidden()
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
        $editorVersionOption = new Options\EditorVersionOption();
        $publicTokenOption   = new Options\PublicTokenOption();
        $ampStylesOption     = new Options\AMP\AMPStylesOption();

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


                'amp_styles' => new Constraints\Optional($ampStylesOption->buildConstraint()),


                'public_token' => new Constraints\Required($publicTokenOption->buildConstraint()),
            ),
            'allowExtraFields' => true,
        ));

        return $constraint;
    }

    /**
     * @return Constraint
     */
    public function buildConstraintsForbidden()
    {
        $ampStylesOption = new Options\AMP\AMPStylesOption();

        $constraint = new Constraints\Collection(array(
            'fields' => array(


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


                'amp_styles' => new Constraints\Optional($ampStylesOption->buildConstraint()),

            ),
            'allowExtraFields' => true,
        ));

        return $constraint;
    }
}

<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI\Actions;

use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Setka\Editor\Admin\Service\SetkaEditorAPI\Errors;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

/**
 * Class SignUpAction
 */
class SignUpAction extends SetkaEditorAPI\Prototypes\ActionAbstract
{
    /**
     * SignUpAction constructor.
     */
    public function __construct()
    {
        $this
            ->setAuthenticationRequired(false)
            ->setMethod(Request::METHOD_POST)
            ->setEndpoint('/api/v1/wordpress/signups.json');
    }

    /**
     * @inheritdoc
     */
    public function configureAndResolveRequestDetails()
    {
        $data = $this->getRequestDetails();

        $resolver = new OptionsResolver();
        $resolver
            ->setRequired('body')
            ->setAllowedTypes('body', 'array');

        $data = $resolver->resolve($data);

        $bodyResolver = new OptionsResolver();
        $bodyResolver
            ->setRequired('signup')
            ->setAllowedTypes('signup', 'array');
        $data['body'] = $bodyResolver->resolve($data['body']);

        $signUpResolver = new OptionsResolver();
        $signUpResolver
            ->setRequired('company_type')
            ->setDefault('company_type', 'person')
            ->setAllowedValues('company_type', array('person', 'company'))

            ->setRequired('email')
            ->setAllowedTypes('email', 'string')

            ->setRequired('first_name')
            ->setAllowedTypes('first_name', 'string')

            ->setRequired('last_name')
            ->setAllowedTypes('last_name', 'string')

            ->setRequired('region')
            ->setAllowedTypes('region', 'string')

            ->setRequired('company_domain')
            ->setAllowedTypes('company_domain', 'string')

            ->setRequired('password')
            ->setAllowedTypes('password', 'string')

            // Company stuff

            ->setDefined('company_name')
            ->setAllowedTypes('company_name', 'string')

            ->setDefined('company_size')
            ->setAllowedTypes('company_size', 'string')

            ->setDefined('company_department')
            ->setAllowedTypes('company_department', 'string')

            // Allow additional info such as body.signup.current_wordpress_theme
            ->setDefined(array_keys($data['body']['signup']));
        ;

        $data['body']['signup'] = $signUpResolver->resolve($data['body']['signup']);

        $this->setRequestDetails($data);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function handleResponse()
    {
        $response = $this->getResponse();

        switch($response->getStatusCode()) {
            case $response::HTTP_CREATED:
                $this->validateOk($response->content);
                break;

            case $response::HTTP_UNPROCESSABLE_ENTITY:
                $helper = new SetkaEditorAPI\Helpers\ErrorHelper($this->getApi(), $response, $this->getErrors());
                $helper->handleResponse();
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
     * @return Constraint[]
     */
    public function buildConstraintsOk()
    {
        $requestDetails = $this->getRequestDetails();
        return array(
            new Constraints\NotBlank(),
            new Constraints\Collection(array(
                'fields' => array(
                    'email' => array(
                        new Constraints\NotBlank(),
                        new Constraints\IdenticalTo(array(
                            'value' => $requestDetails['body']['signup']['email'],
                        )),
                    ),
                    'first_name' => array(
                        new Constraints\NotBlank(),
                        new Constraints\IdenticalTo(array(
                            'value' => $requestDetails['body']['signup']['first_name'],
                        )),
                    ),
                    'last_name' => array(
                        new Constraints\NotBlank(),
                        new Constraints\IdenticalTo(array(
                            'value' => $requestDetails['body']['signup']['last_name'],
                        )),
                    ),
                ),
                'allowExtraFields' => true,
            )),
        );
    }
}

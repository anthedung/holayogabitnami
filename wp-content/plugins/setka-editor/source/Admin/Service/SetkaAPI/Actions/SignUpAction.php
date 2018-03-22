<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Actions;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Service\SetkaAPI\Errors;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class SignUpAction extends SetkaAPI\Prototypes\ActionAbstract
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

    public function getConstraint()
    {
        $requestDetails = $this->getRequestDetails();
        return new Constraints\Collection(array(
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
        ));
    }

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

    public function handleResponse()
    {
        $response = $this->getResponse();

        switch($response->getStatusCode()) {
            // 201 // OK!
            case $response::HTTP_CREATED:
                /**
                 * @var $content ParameterBag
                 */
                $content = $response->getContent();

                $validator = $this->getApi()->getValidator();
                try {
                    $results = $validator->validate($content->all(), $this->getConstraint());
                    $this->violationsToException($results);
                } catch (\Exception $exception) {
                    $this->getErrors()->add(new Errors\ResponseBodyInvalidError());
                    return;
                }
                break;

            // 422 // Invalid data
            case $response::HTTP_UNPROCESSABLE_ENTITY:
                $errorsHelper = new SetkaAPI\Helpers\ErrorHelper($this->getApi(), $response, $this->getErrors());
                $errorsHelper->handleResponse();
                break;

            default:
                $this->getErrors()->add(new Errors\UnknownError());
                break;
        }
    }
}

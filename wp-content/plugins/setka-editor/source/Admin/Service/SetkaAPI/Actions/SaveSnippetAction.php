<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Actions;

use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Service\SetkaAPI\Errors;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaveSnippetAction extends SetkaAPI\Prototypes\ActionAbstract
{

    /**
     * SaveSnippetAction constructor.
     */
    public function __construct()
    {
        $this
            ->setMethod(Request::METHOD_POST)
            ->setEndpoint('/api/v1/wordpress/snippets.json');
    }

    public function getConstraint()
    {
    }

    public function configureAndResolveRequestDetails()
    {
        $data = $this->getRequestDetails();

        $resolver = new OptionsResolver();
        $resolver
            ->setRequired('body')
            ->setAllowedTypes('body', 'array')
            ->setDefined(array_keys($data));

        $data = $resolver->resolve($data);

        $bodyResolver = new OptionsResolver();
        $bodyResolver
            ->setRequired('snippet')
            ->setAllowedTypes('snippet', 'array')
            ->setDefined(array_keys($data['body']));

        $data['body'] = $bodyResolver->resolve($data['body']);

        $snippetResolver = new OptionsResolver();
        $snippetResolver
            ->setRequired('code')
            ->setAllowedTypes('code', 'string')

            ->setRequired('id')
            ->setAllowedTypes('id', array('string', 'int'))

            ->setRequired('name')
            ->setAllowedTypes('name', 'string')

            ->setDefined(array_keys($data['body']['snippet']));

        $data['body']['snippet'] = $snippetResolver->resolve($data['body']['snippet']);

        $this->setRequestDetails($data);

        return $this;
    }

    public function handleResponse()
    {
        $response = $this->getResponse();

        switch($response->getStatusCode()) {
            // 201 // OK!
            case $response::HTTP_CREATED:
                // Check for valid response content
                // for now we don't check anything because we don't use this data
                break;

            // 400 // Snippet name or snippet body missed
            case $response::HTTP_BAD_REQUEST:
                $this->getErrors()->add(new Errors\Snippet\InvalidRequestData());
                break;

            // 422 // snippet too large
            case $response::HTTP_UNPROCESSABLE_ENTITY:
                $this->getErrors()->add(new Errors\Snippet\ExceedsTheLimitSymbols());
                break;

            // 500 // Setka server technical errors
            case $response::HTTP_INTERNAL_SERVER_ERROR:
            case $response::HTTP_BAD_GATEWAY:
            case $response::HTTP_GATEWAY_TIMEOUT:
                $this->getErrors()->add(new Errors\InternalServerError());
                break;

            default:
                $this->getErrors()->add(new Errors\Snippet\UnknownError());
                break;
        }
    }
}

<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI;

class ResponseFactory
{

    /**
     * @param mixed $response WordPress response array from wp_remote_request (or similar functions).
     *
     * @return Response Object which you can use.
     */
    public static function create($response = null)
    {

        if(null === $response) {
            return new Response();
        }

        // Body
        if(!isset($response['body']) || !is_string($response['body'])) {
            throw new \InvalidArgumentException();
        }

        // Status code
        if(!isset($response['response']['code'])) {
            throw new \InvalidArgumentException('Cant find the HTTP status code in response.');
        }

        // Headers
        // WP >= 4.6
        if(isset($response['headers']) && is_a($response['headers'], 'Requests_Utility_CaseInsensitiveDictionary')) {
            /**
             * @var array $response {
             *     @var $headers \Requests_Utility_CaseInsensitiveDictionary
             * }
             */
            $headers = $response['headers']->getAll();
        }
        // WP < 4.6
        elseif(isset($response['headers']) && is_array($response['headers'])) {
            $headers = $response['headers'];
        } else {
            $headers = array();
        }

        return new Response($response['body'], $response['response']['code'], $headers);
    }
}

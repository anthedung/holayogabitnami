<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI;

use Symfony\Component\HttpFoundation;

/**
 * This object represents the answer from Setka API.
 *
 * Ok I know what HttpFoundation\Response represent other logic object
 * but we use it just for write less code (and HTTPFoundation not using any Traits).
 */
class Response extends HttpFoundation\Response
{

    /**
     * @var string|HttpFoundation\ParameterBag
     */
    public $content;

    /**
     * Response constructor.
     *
     * @param string|HttpFoundation\ParameterBag $content
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     */
    public function __construct($content = '', $status = 200, $headers = array())
    {
        if(is_array($content)) {
            parent::__construct('', $status, $headers);
            $this->content = new HttpFoundation\ParameterBag($content);
        } else {
            parent::__construct($content, $status, $headers);
        }
    }


    /**
     * Transforms JSON string into ParameterBag instance.
     *
     * @throws \InvalidArgumentException If content not a JSON string.
     */
    public function parseContent()
    {
        if(is_a($this->content, HttpFoundation\ParameterBag::class)) {
            return $this;
        }

        if(0 === strpos($this->headers->get('Content-Type'), 'application/json')) {
            $json  = json_decode($this->content, true);
            $error = json_last_error();

            if(JSON_ERROR_NONE === $error && is_array($json)) {
                $this->content = new HttpFoundation\ParameterBag($json);
            } else {
                throw new \InvalidArgumentException('The response body contain invalid JSON data');
            }
        } else {
            throw new \InvalidArgumentException('The response body format not supported');
        }

        return $this;
    }

    /**
     * Gets the current response content.
     *
     * @return HttpFoundation\ParameterBag Content
     */
    public function getContent()
    {
        return $this->content;
    }

    public function __toString()
    {
    }

    public function prepare(\Symfony\Component\HttpFoundation\Request $request)
    {
    }
    public function sendHeaders()
    {
    }

    public function sendContent()
    {
    }
    public function send()
    {
    }

    public function setProtocolVersion($version)
    {
    }
    public function getProtocolVersion()
    {
    }

    public static function closeOutputBuffers($targetLevel, $flush)
    {
    }
}

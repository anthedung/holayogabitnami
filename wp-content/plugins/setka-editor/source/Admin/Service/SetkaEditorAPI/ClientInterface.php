<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI;

/**
 * Interface ClientInterface
 */
interface ClientInterface
{
    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     * @return $this For chain calls.
     */
    public function setUrl($url);

    /**
     * @return array
     */
    public function getDetails();

    /**
     * @param array $details
     * @return $this For chain calls.
     */
    public function setDetails($details);

    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @param mixed $result
     * @return $this For chain calls.
     */
    public function setResult($result);

    /**
     * Make request and save results as $this->result.
     * @return $this For chain calls.
     */
    public function request();
}

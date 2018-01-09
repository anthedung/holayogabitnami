<?php
namespace Setka\Editor\Admin\Service\SetkaAPI;

abstract class AbstractClient implements ClientInterface {

    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $details;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @inheritdoc
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDetails() {
        return $this->details;
    }

    /**
     * @inheritdoc
     */
    public function setDetails($details) {
        $this->details = $details;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * @inheritdoc
     */
    public function setResult($result) {
        $this->result = $result;
        return $this;
    }
}

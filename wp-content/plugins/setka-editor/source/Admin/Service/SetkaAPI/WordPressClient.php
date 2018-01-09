<?php
namespace Setka\Editor\Admin\Service\SetkaAPI;

class WordPressClient extends AbstractClient implements ClientInterface {

    /**
     * @inheritdoc
     */
    public function request() {
        return $this
            ->setResult(null)
            ->setResult(wp_remote_request($this->getUrl(), $this->getDetails()));
    }
}

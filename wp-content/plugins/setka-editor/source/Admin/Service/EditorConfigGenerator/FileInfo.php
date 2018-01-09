<?php
namespace Setka\Editor\Admin\Service\EditorConfigGenerator;

use Setka\Editor\Admin\Service\EditorConfigGenerator\Exceptions\ParsingConfigPathException;

class FileInfo {

    /**
     * @var \WP_Post
     */
    protected $post;

    /**
     * @var string Path to root folder where files stored.
     */
    protected $rootPath;

    /**
     * @var string Url to rootPath.
     */
    protected $rootUrl;

    /**
     * @var string Sub path to file which we want use (for creating URL)
     */
    protected $subPath;

        /**
         * @var array With info about file.
         *
         * @see preparePaths()
         */
        protected $subPathInfo;

        /**
         * @var string Sub path to file which we want make (for creating URL)
         */
        protected $subPathLocal;

    public function __construct(\WP_Post $post, $rootPath, $rootUrl, $subPath) {

        $rootUrl = untrailingslashit($rootUrl);
        $subPath = ltrim($subPath, '/');

        $this
            ->setRootPath($rootPath)
            ->setRootUrl($rootUrl)
            ->setSubPath($subPath)
            ->preparePaths();

    }

    public function preparePaths() {

        $info = pathinfo($this->getSubPath());

        if(!isset($info['dirname']) || !isset($info['extension']) || !isset($info['filename']))
            throw new ParsingConfigPathException();

        $this->subPathInfo = $info;

        $this->setSubPathLocal($info['dirname'] . '/' . $info['filename'] . '-local.' . $info['extension']);

        return $this;
    }

    /**
     * @return string
     */
    public function getRootPath() {
        return $this->rootPath;
    }

    /**
     * @param string $rootPath
     *
     * @return $this For chain calls.
     */
    public function setRootPath($rootPath) {
        $this->rootPath = $rootPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubPath() {
        return $this->subPath;
    }

    /**
     * @param string $subPath
     *
     * @return $this For chain calls.
     */
    public function setSubPath($subPath) {
        $this->subPath = $subPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubPathLocal() {
        return $this->subPathLocal;
    }

    /**
     * @param string $subPathLocal
     *
     * @return $this For chain calls.
     */
    public function setSubPathLocal($subPathLocal) {
        $this->subPathLocal = $subPathLocal;
        return $this;
    }

    /**
     * @return string
     */
    public function getRootUrl() {
        return $this->rootUrl;
    }

    /**
     * @param string $rootUrl
     *
     * @return $this For chain calls.
     */
    public function setRootUrl($rootUrl) {
        $this->rootUrl = $rootUrl;
        return $this;
    }

    public function getPath() {
        return path_join($this->getRootPath(), $this->getSubPath());
    }

    public function getUrl() {
        return $this->getRootUrl() . '/' . $this->getSubPath();
    }

    public function getPathLocal() {
        return path_join($this->getRootPath(), $this->getSubPathLocal());
    }

    public function getUrlLocal() {
        return $this->getRootUrl() . '/' . $this->getSubPathLocal();
    }
}

<?php
namespace Setka\Editor\Admin\Service\EditorConfigGenerator;

use Setka\Editor\Admin\Options\ThemeResourceCSSLocalOption;
use Setka\Editor\Admin\Options\ThemeResourceJSLocalOption;
use Setka\Editor\Admin\Options\Files\UseLocalFilesOption;
use Setka\Editor\Admin\Service\EditorConfigGenerator\Exceptions\ConfigFileEntryException;
use Setka\Editor\Admin\Service\EditorConfigGenerator\Exceptions\DecodingJSONException;
use Setka\Editor\Admin\Service\EditorConfigGenerator\Exceptions\EncodingJSONException;
use Setka\Editor\Admin\Service\EditorConfigGenerator\Exceptions\ReadingConfigFileException;
use Setka\Editor\Admin\Service\EditorConfigGenerator\Exceptions\WritingConfigFileException;
use Setka\Editor\Admin\Service\Filesystem\FilesystemInterface;
use Setka\Editor\PostMetas\FileSubPathPostMeta;

/**
 * Class EditorConfigGenerator
 */
class EditorConfigGenerator
{

    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var string Path to folder where all setka-editor assets files located.
     */
    protected $rootPath;

    /**
     * @var string URL to folder where all setka-editor assets files located.
     */
    protected $rootUrl;

    /**
     * @var FileInfo
     */
    protected $themeResourceJSONFileInfo;

    /**
     * @var FileInfo
     */
    protected $themeResourceCSSFileInfo;

    /**
     * @var FileSubPathPostMeta
     */
    protected $fileSubPathMeta;

    /**
     * @var UseLocalFilesOption
     */
    protected $useLocalFilesOption;

    /**
     * @var ThemeResourceJSLocalOption
     */
    protected $themeResourceJSLocalOption;

    /**
     * @var ThemeResourceCSSLocalOption
     */
    protected $themeResourceCSSLocalOption;

    /**
     * @var array
     */
    protected $config;

    public function __construct(
        FilesystemInterface $filesystem,
        $rootPath,
        $rootUrl,
        \WP_Query $queryJSON,
        \WP_Query $queryCSS,
        FileSubPathPostMeta $fileSubPathMeta,
        UseLocalFilesOption $useLocalFilesOption,
        ThemeResourceJSLocalOption $themeResourceJSLocalOption,
        ThemeResourceCSSLocalOption $themeResourceCSSLocalOption
    ) {
        $this->filesystem = $filesystem;
        $this->rootPath   = $rootPath;
        $this->rootUrl    = trailingslashit($rootUrl);

        if(!$queryJSON->have_posts() || !$queryCSS->have_posts()) {
            throw new ConfigFileEntryException();
        }

        $queryJSON->the_post();
        $post = get_post();

        $fileSubPathMeta->setPostId($post->ID);
        $this->themeResourceJSONFileInfo = new FileInfo(
            $post,
            $this->getRootPath(),
            $this->getRootUrl(),
            $fileSubPathMeta->get()
        );

        $queryCSS->the_post();
        $post = get_post();

        $fileSubPathMeta->setPostId($post->ID);
        $this->themeResourceCSSFileInfo = new FileInfo(
            $post,
            $this->getRootPath(),
            $this->getRootUrl(),
            $fileSubPathMeta->get()
        );

        unset($post);
        wp_reset_postdata();

        $this->fileSubPathMeta             = $fileSubPathMeta;
        $this->useLocalFilesOption         = $useLocalFilesOption;
        $this->themeResourceJSLocalOption  = $themeResourceJSLocalOption;
        $this->themeResourceCSSLocalOption = $themeResourceCSSLocalOption;
    }

    /**
     * Generates the JSON config for Setka Editor and setups the links to local files.
     *
     * @throws \Exception Different exceptions, see methods called from this method.
     *
     * @return $this For chain calls.
     */
    public function generate()
    {
        $this->loadJSON();
        $this->replaceUrls();
        $this->saveJSON();
        $this->enableLocalUsage();
        return $this;
    }

    /**
     * Loads JSON from file into local variable as array.
     *
     * @return $this For chain calls.
     * @throws DecodingJSONException If JSON file is broken.
     * @throws ReadingConfigFileException If filesystem can't read the file content.
     */
    protected function loadJSON()
    {
        $fs = $this->getFilesystem();

        $fileContent = $fs->getFilesystem()
                          ->get_contents($this->themeResourceJSONFileInfo->getPath());

        if(!is_string($fileContent)) {
            throw new ReadingConfigFileException();
        }

        $json = json_decode($fileContent, true);

        if(json_last_error() !== JSON_ERROR_NONE) {
            throw new DecodingJSONException();
        }

        $this->config = $json;

        return $this;
    }

    /**
     * Replaces all urls in config to new.
     *
     * @see replaceUrlsHandler
     *
     * @return $this For chain calls.
     */
    protected function replaceUrls()
    {
        array_walk_recursive($this->config, array($this, 'replaceUrlsHandler'));
        return $this;
    }

    /**
     * Actually makes replacements in $this->config.
     *
     * @param $item string|int The name of array cell.
     * @param $key mixed The value of array cell.
     */
    public function replaceUrlsHandler(&$item, $key)
    {

        // A hack for single file.
        if('public_js_url' === $key) {
            return;
        }

        // Urls have only string type

        if(!is_string($item) || empty($item)) {
            return;
        }

        $startsWith = substr($key, 0, 1);

        if(!$startsWith) {
            return;
        }

        if('_' === $startsWith) {
            return;
        }

        unset($startsWith);


        // Search for _url at the end of key.
        $endsWith = substr($key, -3);
        if(!$endsWith) {
            return;
        }
        if('url' !== $endsWith) {
            return;
        }

        // Since we supporting only PHP > 5.5.9 parse_url() works pretty similar
        // on all versions above and there is no need to use wp_parse_url()
        // which is created for support older PHP versions.
        $scheme = parse_url($item, PHP_URL_SCHEME);
        $host   = parse_url($item, PHP_URL_HOST);
        $path   = parse_url($item, PHP_URL_PATH);

        if(!$scheme || !$host || !$path) {
            return;
        }

        $item = $this->getRootUrl() . ltrim($path, '/');
    }

    /**
     * Saves $this->config as JSON on the disk.
     *
     * @return $this For chain calls.
     * @throws EncodingJSONException If cant encode config into JSON string.
     * @throws WritingConfigFileException If filesystem can't write string into file.
     */
    protected function saveJSON()
    {
        $fs   = $this->getFilesystem();
        $json = wp_json_encode($this->config);

        if(!$json) {
            throw new EncodingJSONException();
        }

        $result = $fs->getFilesystem()
                     ->put_contents($this->themeResourceJSONFileInfo->getPathLocal(), $json);

        if(!$result) {
            throw new WritingConfigFileException();
        }

        return $this;
    }

    /**
     * Enable usage of local files.
     *
     * @return $this For chain calls.
     */
    protected function enableLocalUsage()
    {
        $this->themeResourceJSLocalOption
            ->updateValue($this->themeResourceJSONFileInfo->getUrlLocal());

        $this->themeResourceCSSLocalOption
            ->updateValue($this->themeResourceCSSFileInfo->getUrl());

        $this->useLocalFilesOption
            ->updateValue(true);

        return $this;
    }

    /**
     * @return FilesystemInterface
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * @return string
     */
    public function getRootUrl()
    {
        return $this->rootUrl;
    }
}

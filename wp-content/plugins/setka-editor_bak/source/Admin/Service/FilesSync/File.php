<?php
namespace Setka\Editor\Admin\Service\FilesSync;

use Setka\Editor\Admin\Service\FilesSync\Exceptions\ErrorWhileUpdatingPostException;
use Setka\Editor\Admin\Service\FilesSync\Exceptions\ErrorWhileUpdatingPostMetaException;
use Setka\Editor\Admin\Service\FilesSync\Exceptions\LimitDownloadingAttemptsException;
use Setka\Editor\Admin\Service\FilesSync\Exceptions\OriginUrlWithoutPathException;
use Setka\Editor\PostMetas\AttemptsToDownloadPostMeta;
use Setka\Editor\PostMetas\FileSubPathPostMeta;
use Setka\Editor\PostMetas\OriginUrlPostMeta;
use Setka\Editor\Service\PostStatuses;

class File
{

    /**
     * @var \WP_Post
     */
    protected $post;

    /**
     * @var OriginUrlPostMeta
     */
    protected $originUrlMeta;

    /**
     * @var AttemptsToDownloadPostMeta
     */
    protected $attemptsToDownloadMeta;

    /**
     * @var FileSubPathPostMeta
     */
    protected $fileSubPathMeta;

    /**
     * @var string Mime type of file.
     */
    protected $mime;

    /**
     * @var string Path to file from URL with filename (and extension).
     *
     * For example if file have origin_url like
     * https://example.com/assets/images/newsletter-submit.png
     * then this variable will store "assets/images/newsletter-submit.png" (without lead slash).
     */
    protected $subPathToFile;

    /**
     * @var string Filename with extension. For example: "newsletter-submit.png".
     */
    protected $filename;

    /**
     * @var string Path to file with filename (and extension).
     */
    protected $currentPath;

    /**
     * @var int Number of download attempts.
     */
    protected $downloadAttempts = 3;

    /**
     * File constructor.
     *
     * @param \WP_Post $post
     * @param $originUrlMeta OriginUrlPostMeta
     * @param $attemptsToDownloadMeta AttemptsToDownloadPostMeta
     * @param $fileSubPathMeta FileSubPathPostMeta
     * @param $downloadAttempts int
     *
     * @throws \Exception
     */
    public function __construct(
        \WP_Post $post,
        OriginUrlPostMeta $originUrlMeta,
        AttemptsToDownloadPostMeta $attemptsToDownloadMeta,
        FileSubPathPostMeta $fileSubPathMeta,
        $downloadAttempts
    ) {
        $this->post = $post;

        $this->originUrlMeta = $originUrlMeta;
        $this->originUrlMeta->setPostId($post->ID);

        $this->attemptsToDownloadMeta = $attemptsToDownloadMeta;
        $this->attemptsToDownloadMeta->setPostId($post->ID);

        $this->fileSubPathMeta = $fileSubPathMeta;
        $this->fileSubPathMeta->setPostId($post->ID);

        $this->downloadAttempts = $downloadAttempts;

        $this
            ->setupPathToFileFromUrl()
            ->setupFilename();
    }

    /**
     * @return \WP_Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return $this
     * @throws OriginUrlWithoutPathException
     */
    protected function setupPathToFileFromUrl()
    {
        $url  = $this->getOriginUrl();
        $path = parse_url($url, PHP_URL_PATH);

        if(!is_string($path)) {
            throw new OriginUrlWithoutPathException();
        }

        // Remove lead slash since this is not root path in WordPress.
        $path = ltrim($path, '/');

        $this->subPathToFile = $path;

        return $this;
    }

    /**
     * Returns URL of remote file.
     *
     * @return string Url of remote file.
     */
    public function getOriginUrl()
    {
        return $this->originUrlMeta->get();
    }

    /**
     * Returns a path to file.
     *
     * @return string File path as string.
     */
    public function getPathToFile()
    {
        return $this->subPathToFile;
    }

    /**
     * @return OriginUrlPostMeta
     */
    public function getOriginUrlMeta()
    {
        return $this->originUrlMeta;
    }

    /**
     * @return AttemptsToDownloadPostMeta
     */
    public function getAttemptsToDownloadMeta()
    {
        return $this->attemptsToDownloadMeta;
    }

    /**
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @param string $mime
     * @return $this For chain calls.
     */
    public function setMime($mime)
    {
        $this->mime = $mime;
        return $this;
    }

    /**
     * Explode filename + extension from Origin URL meta and save it to $this->filename.
     *
     * @return $this For chain calls.
     */
    protected function setupFilename()
    {
        // TODO: what if no basename?
        $url            = $this->getOriginUrl();
        $name           = basename($url);
        $this->filename = $name;
        return $this;
    }

    /**
     * Returns filename with extension.
     *
     * @return string Filename with extension.
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getCurrentPath()
    {
        return $this->currentPath;
    }

    /**
     * @param string $currentPath
     */
    public function setCurrentPath($currentPath)
    {
        $this->currentPath = $currentPath;
    }

    public function markAsDownloaded()
    {

        $pathToFile = $this->getPathToFile();

        // Check that meta is not equals (since WP returns false while you trying update meta with same value).
        if($this->fileSubPathMeta->get() !== $pathToFile) {
            $result = $this->fileSubPathMeta->updateValue($this->getPathToFile());

            if(!$result) {
                throw new ErrorWhileUpdatingPostMetaException();
            }
        }

        $result = wp_update_post(array(
            'ID' => $this->post->ID,
            'post_status' => PostStatuses::PUBLISH,
        ));

        if($result !== $this->post->ID) {
            $message = '';

            if(is_wp_error($result)) {
                $message = sprintf(
                    __('Cant update post status (mark as downloaded) for post with ID = %1$s. Result of updating: %2$s %3$s.'),
                    $this->post->ID,
                    $result->get_error_code(),
                    $result->get_error_message()
                );
            }

            throw new ErrorWhileUpdatingPostException($message);
        }

        return $this;
    }

    /**
     * Mark file as pending.
     *
     * Will be tried downloaded again in the next time.
     *
     * @return $this
     * @throws ErrorWhileUpdatingPostException
     * @throws ErrorWhileUpdatingPostMetaException
     * @throws LimitDownloadingAttemptsException
     */
    public function markAsPending()
    {
        $result = wp_update_post(array(
            'ID' => $this->post->ID,
            'post_status' => PostStatuses::PENDING,
        ));

        if($result !== $this->post->ID) {
            $message = '';

            if(is_wp_error($result)) {
                $message = sprintf(
                    __('Cant update post status (mark as pending) for post with ID = %1$s. Result of updating: %2$s %3$s.'),
                    $this->post->ID,
                    $result->get_error_code(),
                    $result->get_error_message()
                );
            }

            throw new ErrorWhileUpdatingPostException($message);
        }

        $counter = $this->attemptsToDownloadMeta->get();
        $counter = (int) $counter;
        $counter++;
        $result = $this->attemptsToDownloadMeta->updateValue($counter);

        if(!$result) {
            throw new ErrorWhileUpdatingPostMetaException();
        }

        if($counter > $this->downloadAttempts) {
            throw new LimitDownloadingAttemptsException();
        }

        return $this;
    }

    public function updateCurrentFileMimeType()
    {
        $result = wp_update_post(array(
            'ID' => $this->post->ID,
            'post_mime_type' => $this->getMime(),
        ));

        if($result !== $this->post->ID) {
            $message = '';

            if(is_wp_error($result)) {
                $message = sprintf(
                    __('Cant update post_mime_type for post with ID = %1$s. Result of updating: %2$s %3$s.'),
                    $this->post->ID,
                    $result->get_error_code(),
                    $result->get_error_message()
                );
            }

            throw new ErrorWhileUpdatingPostException($message);
        }

        return $this;
    }
}

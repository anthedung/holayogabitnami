<?php
namespace Setka\Editor\Admin\Service\FilesCreator;

use Setka\Editor\Admin\Options\Files\FilesOption;
use Setka\Editor\Admin\Service\FilesCreator\Exceptions\CantCreateMetaException;
use Setka\Editor\Admin\Service\FilesCreator\Exceptions\CantCreatePostException;
use Setka\Editor\Admin\Service\FilesCreator\Exceptions\UpdatePostException;
use Setka\Editor\PostMetas\OriginUrlPostMeta;
use Setka\Editor\PostMetas\SetkaFileTypePostMeta;
use Setka\Editor\PostMetas\SetkaFileIDPostMeta;
use Setka\Editor\Service\PostStatuses;
use Setka\Editor\Service\SetkaPostTypes;

/**
 * Creates entries for each file from FilesOption.
 *
 * The class check for already existed file entry in DB by URL
 * and if this file exists then post_status updated to draft.
 */
class FilesCreator
{

    /**
     * @var FilesOption
     */
    protected $filesOption;

    /**
     * @var array List of files from $this->filesOption.
     */
    protected $filesList;

    /**
     * @var OriginUrlPostMeta
     */
    protected $originUrlMeta;

    /**
     * @var SetkaFileIDPostMeta
     */
    protected $setkaFileIDMeta;

    /**
     * @var SetkaFileTypePostMeta
     */
    protected $setkaFileTypeMeta;

    /**
     * @var callable Callback which checked after each iteration in $this->syncFiles().
     */
    protected $continueExecution;

    /**
     * PostCreator constructor.
     */
    public function __construct(FilesOption $filesOption)
    {
        $this->originUrlMeta     = new OriginUrlPostMeta();
        $this->setkaFileIDMeta   = new SetkaFileIDPostMeta();
        $this->setkaFileTypeMeta = new SetkaFileTypePostMeta();
        $this->filesOption       = $filesOption;
    }

    /**
     * Creates the posts in DB.
     *
     * @see createPostsHandler
     *
     * @throws \Exception
     *
     * @return mixed Result from other method.
     */
    public function createPosts()
    {
        try {
            return $this->createPostsHandler();
        }
        finally {
            // If exception will throwed we need restore globals back
            wp_reset_postdata(); // restore globals back
        }
    }

    /**
     * Creates the file entries if they not exists.
     *
     * Or update post_status to draft if this entry exists.
     *
     * @return $this For chain calls.
     * @throws CantCreateMetaException
     * @throws CantCreatePostException
     * @throws UpdatePostException
     */
    protected function createPostsHandler()
    {
        $this->filesList = $this->filesOption->get();

        if(empty($this->filesList)) {
            return $this;
        }

        foreach($this->filesList as $item) {
            $query = new \WP_Query(array(
                'post_type' => SetkaPostTypes::FILE_POST_NAME,
                'post_status' => PostStatuses::ANY,
                'meta_query' => array(
                    array(
                        'key' => $this->originUrlMeta->getName(),
                        'value' => $item['url'],
                    ),
                ),

                // Don't save result into cache since this used only by cron.
                'cache_results' => false,

                'posts_per_page' => 1,
            )); // WPCS: slow query ok.

            // Check can we do next iteration
            call_user_func($this->continueExecution);

            // Check if file already exists?
            if($query->have_posts()) {
                // Update existing entry in DB
                $query->the_post();
                $post = get_post();

                if(PostStatuses::ARCHIVE === $post->post_status) {
                    $post->post_status = PostStatuses::DRAFT;
                    $result            = wp_update_post($post);

                    if(is_int($result) && $result > 0) {
                        continue;
                    } else {
                        throw new UpdatePostException();
                    }
                }
            }
            else {
                // Create new post. Draft means that file not downloaded.
                $postID = wp_insert_post(array(
                    'post_type' => SetkaPostTypes::FILE_POST_NAME,
                    'post_status' => PostStatuses::DRAFT,
                ));

                if(is_int($postID) && $postID > 0) {
                    $this->originUrlMeta->setPostId($postID);
                    $postMetaURL = $this->originUrlMeta->updateValue($item['url']);
                    $postMetaURL = $this->isPostMetaCreated($postMetaURL);

                    $this->setkaFileIDMeta->setPostId($postID);
                    $postMetaSetkaID = $this->setkaFileIDMeta->updateValue($item['id']);
                    $postMetaSetkaID = $this->isPostMetaCreated($postMetaSetkaID);

                    $this->setkaFileTypeMeta->setPostId($postID);
                    $postMetaSetkaFileType = $this->setkaFileTypeMeta->updateValue($item['filetype']);
                    $postMetaSetkaFileType = $this->isPostMetaCreated($postMetaSetkaFileType);

                    if(!$postMetaURL || !$postMetaSetkaID || !$postMetaSetkaFileType) {
                        throw new CantCreateMetaException();
                    }
                } else {
                    throw new CantCreatePostException();
                }
            }
        }

        return $this;
    }

    /**
     * Check if meta saved.
     *
     * @param $meta mixed Result of updating meta.
     *
     * @return bool True if meta created, false otherwise.
     */
    protected function isPostMetaCreated($meta)
    {
        if((is_int($meta) && $meta > 0) || true === $meta) {
            return true;
        }
        return false;
    }

    /**
     * @return callable
     */
    public function getContinueExecution()
    {
        return $this->continueExecution;
    }

    /**
     * @param callable $continueExecution
     *
     * @return $this For chain calls.
     */
    public function setContinueExecution($continueExecution)
    {
        $this->continueExecution = $continueExecution;
        return $this;
    }
}

<?php
namespace Setka\Editor\Admin\Service\FilesCreator;

use Setka\Editor\Admin\Options\Files\FilesOption;
use Setka\Editor\Admin\Service\FilesCreator\Exceptions\CantCreateMetaException;
use Setka\Editor\Admin\Service\FilesCreator\Exceptions\CantCreatePostException;
use Setka\Editor\Admin\Service\FilesCreator\Exceptions\UpdatePostException;
use Setka\Editor\Entries\Meta\OriginUrlMeta;
use Setka\Editor\Entries\Meta\SetkaFileTypeMeta;
use Setka\Editor\Entries\Meta\SetkaFileIDMeta;
use Setka\Editor\Entries\PostStatuses;
use Setka\Editor\Entries\SetkaEditorFilePostType;

/**
 * Creates entries for each file from FilesOption.
 *
 * The class check for already existed file entry in DB by URL
 * and if this file exists then post_status updated to draft.
 */
class FilesCreator {

	/**
	 * @var FilesOption
	 */
	protected $filesOption;

	/**
	 * @var array List of files from $this->filesOption.
	 */
	protected $filesList;

	/**
	 * @var OriginUrlMeta
	 */
	protected $originUrlMeta;

	/**
	 * @var SetkaFileIDMeta
	 */
	protected $setkaFileIDMeta;

	/**
	 * @var SetkaFileTypeMeta
	 */
	protected $setkaFileTypeMeta;

	/**
	 * @var callable Callback which checked after each iteration in $this->syncFiles().
	 */
	protected $continueExecution;

	/**
	 * PostCreator constructor.
	 */
	public function __construct(FilesOption $filesOption) {
		$this->originUrlMeta = new OriginUrlMeta();
		$this->setkaFileIDMeta = new SetkaFileIDMeta();
		$this->setkaFileTypeMeta = new SetkaFileTypeMeta();
		$this->filesOption = $filesOption;
		// TODO: pass post metas via args
	}

    /**
     * Creates the posts in DB.
     *
     * @see createPostsHandler
     *
     * @return mixed Result from other method.
     */
	public function createPosts() {
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
	protected function createPostsHandler() {
		$this->filesList = $this->filesOption->getValue();

		// If no files then all is ok.
		if(empty($this->filesList)) {
			return $this;
		}

		foreach($this->filesList as $item) {

			$query = new \WP_Query(array(
				'post_type' => SetkaEditorFilePostType::NAME,
				'post_status' => PostStatuses::ANY,
				'meta_query' => array(
					array(
						'key' => $this->originUrlMeta->getName(),
						'value' => $item['url'],
					),
				),

				// Don't save result into cache since this used only by cron.
				'cache_results' => false,

				// Only one post.
				'posts_per_page' => 1,
			));

            // Check can we do next iteration
            call_user_func($this->continueExecution);

			// Check if file already exists?
			if($query->have_posts()) {
				// Update existed entry in DB
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
					'post_type' => SetkaEditorFilePostType::NAME,
					'post_status' => PostStatuses::DRAFT,
				));

				if(is_int($postID) && $postID > 0) {

					// Post created. Lets insert our link into this post.
					$this->originUrlMeta->setPostId($postID);
					$postMetaURL = $this->originUrlMeta->updateValue($item['url']);
					$postMetaURL = $this->isPostMetaCreated($postMetaURL);

					$this->setkaFileIDMeta->setPostId($postID);
					$postMetaSetkaID = $this->setkaFileIDMeta->updateValue($item['id']);
					$postMetaSetkaID = $this->isPostMetaCreated($postMetaSetkaID);

					$this->setkaFileTypeMeta->setPostId($postID);
					$postMetaSetkaFileType = $this->setkaFileTypeMeta->updateValue($item['filetype']);
					$postMetaSetkaFileType = $this->isPostMetaCreated($postMetaSetkaFileType);


					// Error on post meta creation.
					if(!$postMetaURL || !$postMetaSetkaID || !$postMetaSetkaFileType) {
						throw new CantCreateMetaException();
					}
				} else {
					// Error on post creation.
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
	protected function isPostMetaCreated($meta) {
		if((is_int($meta) && $meta > 0) || $meta === true) {
			return true;
		}
		return false;
	}

	/**
	 * @return callable
	 */
	public function getContinueExecution() {
		return $this->continueExecution;
	}

	/**
	 * @param callable $continueExecution
	 *
	 * @return $this For chain calls.
	 */
	public function setContinueExecution($continueExecution) {
		$this->continueExecution = $continueExecution;
		return $this;
	}
}
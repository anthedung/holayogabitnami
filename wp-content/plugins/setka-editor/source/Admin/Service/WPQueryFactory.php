<?php
namespace Setka\Editor\Admin\Service;

use Setka\Editor\Entries\Meta\OriginUrlMeta;
use Setka\Editor\Entries\Meta\SetkaFileTypeMeta;
use Setka\Editor\Entries\PostStatuses;
use Setka\Editor\Entries\SetkaEditorFilePostType;

class WPQueryFactory {

	/**
	 * Returns \WP_Query instance with single file marked as draft.
	 *
	 * @return \WP_Query
	 */
	public static function createWhereFilesIsDrafts() {
		$searchDetails = array(
			'post_type' => SetkaEditorFilePostType::NAME,
			'posts_per_page' => 1,
			'post_status' => PostStatuses::DRAFT,

			// Don't save result into cache since this used only by cron.
			'cache_results' => false,
		);

		return new \WP_Query($searchDetails);
	}

	/**
	 * Returns \WP_Query instance with single file marked as pending.
	 *
	 * @return \WP_Query
	 */
	public static function createWhereFilesIsPending() {
		$searchDetails = array(
			'post_type' => SetkaEditorFilePostType::NAME,
			'posts_per_page' => 1,
			'post_status' => PostStatuses::PENDING,

			// Don't save result into cache since this used only by cron.
			'cache_results' => false,
		);

		return new \WP_Query($searchDetails);
	}

    /**
     * @param string $url URL to JSON file
     *
     * @return \WP_Query
     */
	public static function createThemeJSON($url) {

        $originUrlMeta = new OriginUrlMeta();

        return new \WP_Query(array(
            'post_type' => SetkaEditorFilePostType::NAME,
            'post_status' => PostStatuses::PUBLISH,

            'meta_key' => $originUrlMeta->getName(),
            'meta_value' => $url,

            // Don't save result into cache since this used only by cron.
            'cache_results' => false,

            // Only one post.
            'posts_per_page' => 1,
        ));
    }

    /**
     * @param string $url URL to CSS file
     *
     * @return \WP_Query
     */
    public static function createThemeCSS($url) {

        $originUrlMeta = new OriginUrlMeta();

        return new \WP_Query(array(
            'post_type' => SetkaEditorFilePostType::NAME,
            'post_status' => PostStatuses::PUBLISH,

            'meta_key' => $originUrlMeta->getName(),
            'meta_value' => $url,

            // Don't save result into cache since this used only by cron.
            'cache_results' => false,

            // Only one post.
            'posts_per_page' => 1,
        ));
    }
}

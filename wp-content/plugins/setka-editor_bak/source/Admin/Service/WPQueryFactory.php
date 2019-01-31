<?php
namespace Setka\Editor\Admin\Service;

use Setka\Editor\PostMetas\OriginUrlPostMeta;
use Setka\Editor\Service\PostStatuses;
use Setka\Editor\Service\SetkaPostTypes;

class WPQueryFactory
{

    /**
     * Returns \WP_Query instance with single file marked as draft.
     *
     * @return \WP_Query
     */
    public static function createWhereFilesIsDrafts()
    {
        $searchDetails = array(
            'post_type' => SetkaPostTypes::FILE_POST_NAME,
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
    public static function createWhereFilesIsPending()
    {
        $searchDetails = array(
            'post_type' => SetkaPostTypes::FILE_POST_NAME,
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
    public static function createThemeJSON($url)
    {

        $originUrlMeta = new OriginUrlPostMeta();

        return new \WP_Query(array(
            'post_type' => SetkaPostTypes::FILE_POST_NAME,
            'post_status' => PostStatuses::PUBLISH,

            'meta_key' => $originUrlMeta->getName(),
            'meta_value' => $url,

            // Don't save result into cache since this used only by cron.
            'cache_results' => false,

            'posts_per_page' => 1,
        ));
    }

    /**
     * @param string $url URL to CSS file
     *
     * @return \WP_Query
     */
    public static function createThemeCSS($url)
    {

        $originUrlMeta = new OriginUrlPostMeta();

        return new \WP_Query(array(
            'post_type' => SetkaPostTypes::FILE_POST_NAME,
            'post_status' => PostStatuses::PUBLISH,

            'meta_key' => $originUrlMeta->getName(),
            'meta_value' => $url,

            // Don't save result into cache since this used only by cron.
            'cache_results' => false,

            'posts_per_page' => 1,
        )); // WPCS: slow query ok.
    }

    /**
     * Returns \WP_Query instance with single AMP file marked as draft.
     *
     * @return \WP_Query
     */
    public static function createWhereAMPFileIsDraft()
    {
        $searchDetails = array(
            'post_type' => array(SetkaPostTypes::AMP_COMMON, SetkaPostTypes::AMP_THEME, SetkaPostTypes::AMP_LAYOUT),
            'posts_per_page' => 1,
            'post_status' => PostStatuses::DRAFT,
            'orderby' => 'ID',

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
    public static function createWhereAMPFileIsPending()
    {
        $searchDetails = array(
            'post_type' => array(SetkaPostTypes::AMP_COMMON, SetkaPostTypes::AMP_THEME, SetkaPostTypes::AMP_LAYOUT),
            'posts_per_page' => 1,
            'post_status' => PostStatuses::PENDING,
            'orderby' => 'ID',

            // Don't save result into cache since this used only by cron.
            'cache_results' => false,
        );

        return new \WP_Query($searchDetails);
    }

    /**
     * Returns \WP_Query with last created AMP config.
     *
     * @return \WP_Query
     */
    public static function createWhereLastAMPConfig()
    {
        return new \WP_Query(array(
            'post_type' => SetkaPostTypes::AMP_CONFIG,
            'post_status' => PostStatuses::PUBLISH,
            'order' => 'DESC',
            'orderby' => 'ID',
            'posts_per_page' => 1,
        ));
    }
}

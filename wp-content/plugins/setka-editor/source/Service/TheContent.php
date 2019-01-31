<?php
namespace Setka\Editor\Service;

use Setka\Editor\Entries\Meta;

/**
 * Removes some filters before output the_content()
 * and then restore them back for the upcoming posts in loop.
 *
 * Because Setka Editor wrap all content into <p> and other HTML tags automaticly
 * there is no need to parse post content with wpautop(),
 * shortcode_unautop(), prepend_attachment() functions.
 * Must correct working with loop inside other loop.
 *
 * Class TheContent
 * @package Setka\Editor\Service
 */
class TheContent
{

    /**
     * @var array Filters which we handle.
     */
    private static $filters_list = array(
        array(
            'filter' => array(
                'callable' => 'wpautop',
                'priority' => 10,
                'args' => 1
            ),
            'need_to_be_removed' => true,
            'has_filter' => null
        ),
        array(
            'filter' => array(
                'callable' => 'shortcode_unautop',
                'priority' => 10,
                'args' => 1
            ),
            'need_to_be_removed' => true,
            'has_filter' => null
        ),
        array(
            'filter' => array(
                'callable' => 'prepend_attachment',
                'priority' => 10,
                'args' => 1
            ),
            'need_to_be_removed' => true,
            'has_filter' => null
        ),
    );

    /**
     * Simply check if post created with Grid Editor. If yes - remove wpautop() an other similar filters
     * which formatting entry content. We don't need this because Grid Editor already formatting content properly.
     *
     * @since 0.0.1
     *
     * @param $content string Post content from WordPress
     *
     * @return string Not modified post content.
     */
    public static function checkTheContentFilters($content)
    {
        global $post;

        if (!is_a($post, \WP_Post::class)) {
            return $content;
        }

        $use_editor_meta = new Meta\UseEditorMeta();
        $use_editor_meta->setPostId($post->ID);

        if($use_editor_meta->getValue() === '1') {
            self::maybeRemoveWPFilters();
        }

        return $content;
    }

    /**
     * Adds removed filters back.
     *
     * @since 0.0.1
     *
     * @param $content string Post content from WordPress
     *
     * @return string Not modified post content.
     */
    public static function checkTheContentFiltersAfter($content)
    {
        self::maybeRestoreWPFilters();
        return $content;
    }

    /**
     * Removes default filters.
     *
     * @since 0.0.1
     */
    public static function maybeRemoveWPFilters()
    {
        foreach(self::$filters_list as $filter_key => $filter_value) {
            if($filter_value['need_to_be_removed']) {
                $has_filter = has_filter('the_content', $filter_value['filter']['callable']);
                if($has_filter) {
                    self::$filters_list[$filter_key]['has_filter']         = true;
                    self::$filters_list[$filter_key]['filter']['priority'] = $has_filter;

                    remove_filter(
                        'the_content',
                        $filter_value['filter']['callable'],
                        $filter_value['filter']['priority'],
                        self::$filters_list[$filter_key]['filter']['priority']
                    );
                }
                else {
                    self::$filters_list[$filter_key]['has_filter'] = false;
                }
            }
        }
    }

    /**
     * Restore previously removed filters.
     *
     * @since 0.0.1
     */
    public static function maybeRestoreWPFilters()
    {
        foreach(self::$filters_list as $filter_key => $filter_value) {
            if(true === $filter_value['has_filter']) {
                add_filter(
                    'the_content',
                    $filter_value['filter']['callable'],
                    $filter_value['filter']['priority'],
                    $filter_value['filter']['args']
                );
                self::$filters_list[$filter_key]['has_filter'] = false;
            }
        }
    }
}

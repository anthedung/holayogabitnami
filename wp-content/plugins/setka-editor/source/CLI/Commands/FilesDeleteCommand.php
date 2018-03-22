<?php
namespace Setka\Editor\CLI\Commands;

use Setka\Editor\Entries\PostStatuses;
use Setka\Editor\Entries\SetkaEditorFilePostType;
use WP_CLI as Console;

class FilesDeleteCommand extends \WP_CLI_Command
{

    /**
     * This command remove all files and their meta from DB.
     *
     * ## OPTIONS
     *
     * [--confirm]
     * : Set this flag to actually run the command.
     *
     * @when after_wp_load
     */
    public function __invoke($args, $argsv)
    {

        if(true !== $argsv['confirm']) {
            Console::error('Specify confirm flag to run this command.');
            return;
        }

        do {
            $query = new \WP_Query(array(
                'post_type' => SetkaEditorFilePostType::NAME,
                'posts_per_page' => 1,
                'post_status' => PostStatuses::ANY,

                // Don't save result into cache since this used only by cron.
                'cache_results' => false,
            ));

            if($query->have_posts()) {
                $query->the_post();
                $post = get_post();
                Console::log('Deleting post ID = ' . $post->ID);
                wp_delete_post($post->ID, true);
            }

            $query->rewind_posts();
        } while ($query->have_posts());

        wp_reset_postdata();
    }
}

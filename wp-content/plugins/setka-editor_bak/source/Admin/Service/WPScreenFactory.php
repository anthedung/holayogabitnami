<?php
namespace Setka\Editor\Admin\Service;

class WPScreenFactory
{
    /**
     * @return \WP_Screen
     */
    public static function create()
    {
        require_once(ABSPATH . 'wp-admin/includes/class-wp-screen.php');

        if(function_exists('get_current_screen')) {
            $screen = get_current_screen();

            if(is_a($screen, \WP_Screen::class)) {
                return $screen;
            }
        }

        return \WP_Screen::get();
    }
}

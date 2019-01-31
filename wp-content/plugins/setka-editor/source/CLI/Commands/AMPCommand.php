<?php
namespace Setka\Editor\CLI\Commands;

use Korobochkin\WPKit\Options\OptionInterface;
use Setka\Editor\Admin\Cron\AMPStylesCronEvent;
use Setka\Editor\Admin\Cron\AMPStylesQueueCronEvent;
use Setka\Editor\Service\PostStatuses;
use Setka\Editor\Service\SetkaPostTypes;
use WP_CLI as Console;

class AMPCommand extends \WP_CLI_Command
{
    /**
     * @var AMPStylesCronEvent
     */
    protected $ampStylesCronEvent;

    /**
     * @var AMPStylesQueueCronEvent
     */
    protected $ampStylesQueueCronEvent;

    /**
     * @var OptionInterface[]
     */
    protected $options = array();

    /**
     * AMPSyncCommand constructor.
     * @param AMPStylesCronEvent $ampStylesCronEvent
     * @param AMPStylesQueueCronEvent $ampStylesQueueCronEvent
     * @param OptionInterface[] $options
     */
    public function __construct(
        AMPStylesCronEvent $ampStylesCronEvent,
        AMPStylesQueueCronEvent $ampStylesQueueCronEvent,
        array $options
    ) {
        $this->ampStylesCronEvent      = $ampStylesCronEvent;
        $this->ampStylesQueueCronEvent = $ampStylesQueueCronEvent;
        $this->options                 = $options;
    }

    /**
     * @return array List of option names and its values.
     */
    protected function getOptionValues()
    {
        $options = array();
        foreach ($this->options as $option) {
            $options[] = array(
                'Name'  => $option->getName(),
                'Value' => $option->get(),
            );
        }
        return $options;
    }

    /**
     * Show AMP styles sync status.
     *
     * @alias st
     *
     * @when after_wp_load
     */
    public function status()
    {
        $options = $this->getOptionValues();
        \WP_CLI\Utils\format_items('yaml', $options, array('Name', 'Value'));
    }

    /**
     * Restart AMP styles sync
     *
     * @alias res
     *
     * @when after_wp_load
     */
    public function restart()
    {
        $this->ampStylesCronEvent->restart();
        $this->ampStylesQueueCronEvent->restart();
        $this->status();
        Console::success('Restarted.');
    }

    /**
     * Disable AMP styles sync
     *
     * @alias dis
     *
     * @when after_wp_load
     */
    public function disable()
    {
        $this->ampStylesCronEvent->unscheduleAll()->getAmpStylesManager()->resetSync();
        $this->ampStylesQueueCronEvent->unscheduleAll();
        $this->status();
        Console::success('Disabled.');
    }

    /**
     * Enable AMP styles sync
     *
     * @alias en
     *
     * @when after_wp_load
     */
    public function enable()
    {
        $this->restart();
        $this->status();
        Console::success('Enabled.');
    }

    /**
     * Delete all AMP files and options.
     *
     * @when after_wp_load
     */
    public function delete()
    {
        try {
            $this->ampStylesCronEvent->getAmpStylesManager()->deleteAllFiles();
        } catch (\Exception $exception) {
            $message = 'Error while deleting posts:' . PHP_EOL .
                'Exception name: ' . get_class($exception) . PHP_EOL .
                'Exception code: ' . $exception->getCode() . PHP_EOL .
                'Exception message: ' . $exception->getMessage();
            Console::error($message);
        }

        $this->ampStylesCronEvent->unscheduleAll();
        $this->ampStylesQueueCronEvent->unscheduleAll();

        foreach ($this->options as $option) {
            $option->delete();
        }

        Console::success('All files was removed.');
    }

    /**
     * Show all AMP files.
     *
     * @when after_wp_load
     */
    public function show()
    {
        $query = new \WP_Query(array(
            'post_type' => array(SetkaPostTypes::AMP_COMMON, SetkaPostTypes::AMP_THEME, SetkaPostTypes::AMP_LAYOUT, SetkaPostTypes::AMP_CONFIG),
            'posts_per_page' => 100,
            'orderby' => 'ID',
            'post_status' => PostStatuses::ANY,

            // Don't save result into cache since this used only by CLI.
            'cache_results' => false,
        ));

        if(!$query->have_posts()) {
            Console::log('AMP files not found.');
            exit();
        }

        $items = array();

        while($query->have_posts()) {
            $post = $query->next_post();

            $item = array(
                'ID' => $post->ID,
                'post_name' => $post->post_name,
                'post_status' => $post->post_status,
            );

            switch ($post->post_type) {
                case SetkaPostTypes::AMP_COMMON:
                    $item['post_type'] = 'common';
                    break;

                case SetkaPostTypes::AMP_THEME:
                    $item['post_type'] = 'theme';
                    break;

                case SetkaPostTypes::AMP_LAYOUT:
                    $item['post_type'] = 'layout';
                    break;

                case SetkaPostTypes::AMP_CONFIG:
                    $item['post_type'] = 'config';
                    break;

                default:
                    Console::error('Unknown AMP file type in post with ID = ' . $post->ID);
            }

            $items[] = $item;
        }

        \WP_CLI\Utils\format_items('table', $items, array('ID', 'post_name', 'post_status', 'post_type'));
    }
}

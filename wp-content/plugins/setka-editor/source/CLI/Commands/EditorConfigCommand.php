<?php
namespace Setka\Editor\CLI\Commands;

use Setka\Editor\Admin\Service\EditorConfigGenerator\EditorConfigGeneratorFactory;
use Setka\Editor\Admin\Service\EditorConfigGenerator\Exceptions\DecodingJSONException;
use WP_CLI as Console;

class EditorConfigCommand extends \WP_CLI_Command
{

    /**
     * Generate JSON config for Setka Editor.
     *
     * @when after_wp_load
     */
    public function generate()
    {
        try {
            $generator = EditorConfigGeneratorFactory::create();
            $generator->generate();
        }
        catch (DecodingJSONException $exception) {
            Console::log(json_last_error());
            Console::log(json_last_error_msg());
            return;
        }
        catch (\Exception $exception) {
            Console::log(get_class($exception));
            Console::error('Exception throwed while executing.');
            return;
        }

        Console::success('Config generated. WP site switched to local files usage.');
    }
}

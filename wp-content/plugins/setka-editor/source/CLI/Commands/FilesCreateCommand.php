<?php
namespace Setka\Editor\CLI\Commands;

use Setka\Editor\Admin\Service\FilesCreator\FilesCreatorFactory;
use Setka\Editor\Service\SetkaAccount\Account;
use WP_CLI as Console;

class FilesCreateCommand extends \WP_CLI_Command
{

    public function __invoke()
    {
        if(!Account::isLoggedIn()) {
            Console::error('You should sign in first.');
            return;
        }

        $filesCreator = FilesCreatorFactory::createFilesCreator();

        try {
            $filesCreator->createPosts();
        } catch (\Exception $exception) {
            $message = 'An error during creating post. Error type (exception): ' . get_class($exception) . '.';
            Console::error($message);
            return;
        }

        Console::success('Entries for all files successfully created.');
    }
}

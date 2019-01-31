<?php
namespace Setka\Editor\CLI\Commands;

use Setka\Editor\Admin\Service\FilesCreator\FilesCreatorFactory;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;
use WP_CLI as Console;

/**
 * Class FilesCreateCommand
 */
class FilesCreateCommand extends \WP_CLI_Command
{
    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * FilesCreateCommand constructor.
     * @param SetkaEditorAccount $setkaEditorAccount
     */
    public function __construct(SetkaEditorAccount $setkaEditorAccount)
    {
        $this->setkaEditorAccount = $setkaEditorAccount;
    }

    public function __invoke()
    {
        if(!$this->setkaEditorAccount->isLoggedIn()) {
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

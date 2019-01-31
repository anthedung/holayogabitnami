<?php
namespace Setka\Editor\CLI\Commands;

use Setka\Editor\Admin\Service\FilesManager\FilesManager;
use WP_CLI as Console;

/**
 * Class FilesArchiveCommand
 */
class FilesArchiveCommand extends \WP_CLI_Command
{
    /**
     * @var FilesManager
     */
    protected $filesManager;

    /**
     * FilesArchiveCommand constructor.
     * @param FilesManager $filesManager
     */
    public function __construct(FilesManager $filesManager)
    {
        parent::__construct();
        $this->filesManager = $filesManager;
    }

    public function __invoke()
    {
        try {
            $result = $this->filesManager->markAllFilesAsArchived();
            if(is_int($result)) {
                Console::success(sprintf('Successful updated %s file entries in DB.', $result));
            } elseif(false === $result) {
                Console::error('MySQL return an error during request.');
            } else {
                Console::log('Request completed. Result of $wpdb->query() was:');
                // @codingStandardsIgnoreStart
                Console::log(var_export($result, true));
                // @codingStandardsIgnoreEnd
            }
        } catch (\Exception $exception) {
            Console::error_multi_line($this->buildArrayFromException($exception));
            Console::error('An error occurred during execution. See details above.');
        }
    }

    private function buildArrayFromException(\Exception $exception)
    {
        $message = array();

        $message[] = 'Name:   ' . get_class($exception);
        $message[] = 'Message:' . $exception->getMessage();
        $message[] = 'Code:   ' . $exception->getCode();
        $message[] = 'File:   ' . $exception->getFile();
        $message[] = 'Line:   ' . $exception->getLine();

        return $message;
    }
}

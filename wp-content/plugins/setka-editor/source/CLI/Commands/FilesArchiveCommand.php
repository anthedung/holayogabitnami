<?php
namespace Setka\Editor\CLI\Commands;

use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;
use WP_CLI as Console;

class FilesArchiveCommand extends \WP_CLI_Command
{

    public function __invoke()
    {
        try {
            $manager = FilesManagerFactory::create();
            $result  = $manager->markAllFilesAsArchived();
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

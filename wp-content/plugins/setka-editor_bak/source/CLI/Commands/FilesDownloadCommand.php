<?php
namespace Setka\Editor\CLI\Commands;

use Setka\Editor\Admin\Service\FilesSync\FileFactory;
use Setka\Editor\Admin\Service\FilesSync\Synchronizer;
use Setka\Editor\Service\PostStatuses;
use Setka\Editor\Service\SetkaPostTypes;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;
use WP_CLI as Console;

class FilesDownloadCommand extends \WP_CLI_Command
{
    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * @var Synchronizer
     */
    protected $synchronizer;

    /**
     * @var int Number of download attempts.
     */
    protected $downloadAttempts = 3;

    /**
     * FilesDownloadCommand constructor.
     * @param SetkaEditorAccount $setkaEditorAccount
     * @param Synchronizer $synchronizer
     * @param int $downloadAttempts
     */
    public function __construct(
        SetkaEditorAccount $setkaEditorAccount,
        Synchronizer $synchronizer,
        $downloadAttempts
    ) {
        $this->setkaEditorAccount = $setkaEditorAccount;
        $this->synchronizer       = $synchronizer;
        $this->downloadAttempts   = $downloadAttempts;
    }

    /**
     * Download Setka Editor asset file.
     *
     * ## OPTIONS
     *
     * <id>
     * : ID of file in DB to download.
     *
     * @subcommand download
     *
     * @when after_wp_load
     *
     * @param $args array Arguments from terminal input.
     */
    public function download($args)
    {

        if(!$this->setkaEditorAccount->isLoggedIn()) {
            Console::error('You need sign in Setka Editor account before downloading files.');
        }

        $searchDetails = array(
            'page_id' => $args[0],

            'post_type' => SetkaPostTypes::FILE_POST_NAME,
            'posts_per_page' => 1,
            'post_status' => PostStatuses::ANY,

            // Don't save result into cache since this used only by cron.
            'cache_results' => false,
        );

        $query = new \WP_Query($searchDetails);

        if($query->have_posts()) {
            $query->the_post();

            try {
                $file = FileFactory::create(get_post(), $this->downloadAttempts);
                $this->synchronizer
                    ->setCurrentFile($file)
                    ->syncCurrentFile();
            } catch (\Exception $exception) {
                Console::error_multi_line($this->buildArrayFromException($exception));
                Console::error('Error while file downloading.');
            }

            Console::success('Done. File successful downloaded.');
        } else {
            Console::error(sprintf('File entry with ID = %1$s not found. Please checkout the ID.', $args[0]));
        }
    }

    /**
     * Download all Setka Editor files in a queue.
     *
     * @subcommand download-all
     *
     * @when after_wp_load
     */
    public function downloadAll()
    {
        if(!$this->setkaEditorAccount->isLoggedIn()) {
            Console::error('You need sign in Setka Editor account before downloading files.');
        }

        try {
            $this->synchronizer->syncFiles();
        } catch (\Exception $exception) {
            Console::error_multi_line($this->buildArrayFromException($exception));
            Console::error('Can\'t create Synchronizer object. Check filesystem setting and access.');
        }

        Console::success('All files downloaded.');
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

<?php
namespace Setka\Editor\Admin\Service\FilesSync;

use Monolog\Logger;
use Setka\Editor\Admin\Service\FilesSync\Exceptions\AwaitPendingFilesException;
use Setka\Editor\Admin\Service\FilesSync\Exceptions\CantRelocateDownloadedFileException;
use Setka\Editor\Admin\Service\FilesSync\Exceptions\FileDownloadException;
use Setka\Editor\Admin\Service\WPQueryFactory;

class Synchronizer
{

    /**
     * @var FilesystemInterface Object which handle the FS WP object.
     */
    protected $filesystem;

    /**
     * @var $downloader DownloaderInterface|WordPressDownloader Downloader mechanism which can download files.
     */
    protected $downloader;

    /**
     * @var File
     */
    protected $currentFile;

    /**
     * @var string A path to folder where to save files.
     */
    protected $destination;

    /**
     * @var Logger Outputs information about process.
     */
    protected $logger;

    /**
     * @var callable Callback which checked after each iteration in $this->syncFiles().
     */
    protected $continueExecution;

    /**
     * Synchronizer constructor.
     *
     * @param $filesystem FilesystemInterface
     * @param $downloader DownloaderInterface
     * @param $destination string Path to folder where to save files.
     * @param $logger Logger Instance of Logger.
     */
    public function __construct(FilesystemInterface $filesystem, DownloaderInterface $downloader, $destination, Logger $logger)
    {
        $this->filesystem = $filesystem;
        $this->downloader = $downloader;
        $this->setDestination($destination);
        $this->setLogger($logger);
    }

    /**
     * Syncing all files in DB in loop.
     *
     * This method can throws different \Exceptions. Also methods called inside this method
     * also throws \Exceptions. Be sure to wrap this method call into try-catch block.
     *
     * @return $this For chain calls.
     * @throws \Exception Different Exceptions in different scenarios.
     *
     * @see syncCurrentFile()
     */
    public function syncFiles()
    {
        $log = $this->logger;
        $log->info('Start syncing all files.');
        do {
            $query = WPQueryFactory::createWhereFilesIsDrafts();

            call_user_func($this->continueExecution);

            if($query->have_posts()) {
                $query->the_post();
                $post = get_post();
                $log->info('Start syncing file entry.', array(
                    'id' => $post->ID,
                    'post_status' => $post->post_status,
                    'post_mime_type' => $post->post_mime_type,
                ));

                try {
                    $this->currentFile = FileFactory::create($post);
                    $this->syncCurrentFile();
                }
                catch (FileDownloadException $exception) {
                    // If we cant't download file just try next one.
                    continue;
                }
                catch (\Exception $exception) {
                    $log->warning('Exception throwed while syncing file.', array($exception));
                    throw $exception;
                }
                finally {
                    $query->rewind_posts();
                    wp_reset_postdata();
                }

                $log->info('File entry successfully synced.');
            }
        } while ($query->have_posts());

        $pendingQuery = WPQueryFactory::createWhereFilesIsPending();
        if($pendingQuery->have_posts()) {
            throw new AwaitPendingFilesException();
        }

        return $this;
    }

    /**
     * Sync single file instance from $this->currentFile.
     *
     * Helpful if you want sync single file.
     *
     * @throws CantRelocateDownloadedFileException If current Filesystem instance cant relocate the file from tmp folder.
     * @throws \Exception If file downloading failed.
     *
     * @return $this For chain calls.
     */
    public function syncCurrentFile()
    {

        $log  = $this->logger;
        $file = $this->getCurrentFile();

        $log->info('Start downloading file from remote server.', array(
            $file->getOriginUrlMeta()->getName() => $file->getOriginUrlMeta()->getValue(),
            $file->getAttemptsToDownloadMeta()->getName() => $file->getAttemptsToDownloadMeta()->getValue(),
        ));

        try {
            $this->downloadCurrentFile();
        } catch (\Exception $exception) {
            $log->warning('File download was failed. Mark as "pending".', array($exception));

            $file->markAsPending();

            throw $exception;
        }

        $log->info('File downloaded from remote server and saved into temporary PHP folder.');

        // At this moment file have location like /tmp/newsletter-submit-iOHYdY.tmp.
        // If file was not downloaded then exception is throwing and code below will not executed.

        // Save file path into File instance.
        $file->setCurrentPath($this->downloader->getResult());
        $log->info('Temporary file path saved.', array(
            'current_path' => $file->getCurrentPath(),
        ));

        // Update mime type.
        $file
            ->setMime($this->getMimeTypeOfCurrentFile())
            ->updateCurrentFileMimeType();
        $log->info('Detected MIME type.', array(
            'mime_type' => $file->getMime(),
        ));

        //
        //
        //
        //
        // Move file from /tmp/ folder to destination (and rename).

        $this->createDestinationForCurrentFile();
        $log->info('Required folder created for file.');

        $destinationPath = $this->getFullDestinationPath();
        $log->info('Destination path and filename appointed.', array(
            'destination_path' => $destinationPath,
            'current_path' => $file->getCurrentPath(),
        ));

        $moveResult = $this->filesystem
            ->getFilesystem()
                ->move($file->getCurrentPath(), $destinationPath, true);

        if(!$moveResult) {
            $log->warning('Cant relocate (and rename) file into required place.', array(
                'destination_path' => $destinationPath,
                'current_path' => $file->getCurrentPath(),
            ));
            throw new CantRelocateDownloadedFileException();
        }

        $log->info('File moved from temporary folder into destination and renamed.');

        $file->markAsDownloaded();

        $log->info('File marked as downloaded.');

        return $this;
    }

    /**
     * Downloads current file into a temp folder.
     *
     * This method handled by Downloader mechanism.
     *
     * @return $this For chain calls.
     */
    public function downloadCurrentFile()
    {
        $this->downloader
            ->download($this->currentFile->getOriginUrl());

        return $this;
    }

    /**
     * Creates folder (recursive) for current file.
     *
     * @return $this For chain calls.
     */
    public function createDestinationForCurrentFile()
    {
        $path = $this->getFullDestinationPath();

        // Exclude filename and extension.
        $path = dirname($path);

        $this->filesystem->createFoldersRecursive($path);

        return $this;
    }

    /**
     * @return File
     */
    public function getCurrentFile()
    {
        return $this->currentFile;
    }

    /**
     * @param File $currentFile
     *
     * @return $this For chain calls.
     */
    public function setCurrentFile(File $currentFile)
    {
        $this->currentFile = $currentFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     *
     * @return $this For chain calls.
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param Logger $logger
     *
     * @return $this For chain calls.
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return callable
     */
    public function getContinueExecution()
    {
        return $this->continueExecution;
    }

    /**
     * @param callable $continueExecution
     *
     * @return $this For chain calls.
     */
    public function setContinueExecution($continueExecution)
    {
        $this->continueExecution = $continueExecution;
        return $this;
    }

    /**
     * Returns full path where need to save file.
     *
     * @throws \LogicException If no destination was specified.
     *
     * @return string
     */
    public function getFullDestinationPath()
    {
        if(empty($this->destination)) {
            throw new \LogicException('You need specify destination path before sync.');
        }

        $path = $this->currentFile->getPathToFile();

        // Ensure that no leading slash in path since we need to make this path not absolute.
        $path = ltrim($path, '/');

        return path_join($this->destination, $path);
    }

    /**
     * Detects currentFile mime type.
     *
     * @throws \LogicException If called too early.
     *
     * @return string Mime type of current file.
     */
    public function getMimeTypeOfCurrentFile()
    {
        $path = $this->currentFile->getCurrentPath();
        if(!$path) {
            throw new \LogicException('Method getMimeTypeOfCurrentFile should be called only after file downloaded and path to file saved into $this->currentFile->currentPath.');
        }
        return mime_content_type($this->currentFile->getCurrentPath());
    }
}

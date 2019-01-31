<?php
namespace Setka\Editor\Admin\Service\Filesystem;

interface FilesystemInterface
{
    /**
     * Get filesystem object.
     *
     * @return \WP_Filesystem_Base The result may be a class that extended from \WP_Filesystem_Base.
     */
    public function getFilesystem();

    /**
     * Set filesystem object.
     *
     * @param \WP_Filesystem_Base $filesystem \WP_Filesystem_Base instance or extended from it.
     *
     * @return $this For chain calls.
     */
    public function setFilesystem($filesystem);

    /**
     * Creates a folder in recursive manner.
     *
     * @param $path string Folders path which will be created.
     * @return $this For chain calls.
     */
    public function createFoldersRecursive($path);

    /**
     * Read file content into string.
     *
     * @param $path string Path to file.
     *
     * @throws \RuntimeException If read was failed.
     *
     * @return string If file successfully read.
     */
    public function getContents($path);

    /**
     * Delete file or folder.
     *
     * @param $path string Path to file which will be deleted.
     *
     * @throws \RuntimeException If deleting was failed.
     *
     * @return $this For chain calls.
     */
    public function unlink($path);

    /**
     * Check if file or folder exist.
     *
     * @param $path string Path to file or folder.
     *
     * @throws \RuntimeException If WordPress file system object return unexpected result.
     *
     * @return bool True or false in asking to question exists or not.
     */
    public function exists($path);
}

<?php
namespace Setka\Editor\Admin\Service\FilesSync;

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

    public function createFoldersRecursive($path);
}

<?php
namespace Setka\Editor\Admin\Service\Filesystem;

class NullFilesystem implements FilesystemInterface
{
    /**
     * @inheritdoc
     *
     * @throws \RuntimeException This method is not available in this FS.
     */
    public function getFilesystem()
    {
        throw new \RuntimeException();
    }

    /**
     * @inheritdoc
     */
    public function setFilesystem($filesystem)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function createFoldersRecursive($path)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getContents($path)
    {
        throw new \RuntimeException(sprintf('Current file system doesn\'t support this method. File system name: %1$s', get_class($this)));
    }

    /**
     * @inheritdoc
     */
    public function unlink($path)
    {
        throw new \RuntimeException(sprintf('Current file system doesn\'t support this method. File system name: %1$s', get_class($this)));
    }

    /**
     * @inheritdoc
     */
    public function exists($path)
    {
        throw new \RuntimeException(sprintf('Current file system doesn\'t support this method. File system name: %1$s', get_class($this)));
    }
}

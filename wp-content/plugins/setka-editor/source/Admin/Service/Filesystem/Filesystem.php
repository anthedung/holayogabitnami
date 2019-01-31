<?php
namespace Setka\Editor\Admin\Service\Filesystem;

use Setka\Editor\Admin\Service\FilesSync\Exceptions\CantCreateDirectoryException;
use Setka\Editor\Service\PathsAndUrls;

class Filesystem implements FilesystemInterface
{

    /**
     * @var $filesystem \WP_Filesystem_Base
     */
    protected $filesystem;

    /**
     * @inheritdoc
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @inheritdoc
     */
    public function setFilesystem($filesystem)
    {
        $this->filesystem = $filesystem;
        return $this;
    }

    /**
     * Create folder recursive.
     *
     * @param $path string Path which you need to create
     *
     * @throws \Exception If path invalid and can't be splitted into fragments.
     *
     * @throws CantCreateDirectoryException If WordPress (and PHP) cant create required directory.
     *
     * @return $this For chain calls.
     */
    public function createFoldersRecursive($path)
    {

        $fragments = PathsAndUrls::splitUrlPathIntoFragments($path);

        if(empty($fragments)) {
            throw new \Exception('Invalid path. Can\'t find folders.');
        }

        /**
         * For example. We have 10 fragments. But indexes starts from 0...9.
         * This is why we calculating this way: $index = count($fragments) - 1.
         */
        for($index = count($fragments) - 1; $index >= 0; $index--) {
            if(!$this->getFilesystem()->exists($fragments[$index])) {
                $result = $this->getFilesystem()->mkdir($fragments[$index]);
                if(!$result) {
                    throw new CantCreateDirectoryException();
                }
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getContents($path)
    {
        $result = $this->filesystem->get_contents($path);
        if(is_string($result)) {
            return $result;
        }
        throw new \RuntimeException(sprintf('File reading error. File path: %1$s', $path));
    }

    /**
     * @inheritdoc
     */
    public function unlink($path)
    {
        $result = $this->filesystem->delete($path, true);
        if(true === $result) {
            return $this;
        }
        throw new \RuntimeException(sprintf('File or folder deleting error. Path: %1$s', $path));
    }

    /**
     * @inheritdoc
     */
    public function exists($path)
    {
        $result = $this->filesystem->exists($path);
        if(is_bool($result)) {
            return $result;
        }
        throw new \RuntimeException(sprintf('WordPress file system exists() method return unexpected result. Path: %1$s.', $path));
    }
}

<?php
namespace Setka\Editor\Admin\Options\Files;

use Korobochkin\WPKit\Options\Special\BoolOption;
use Setka\Editor\Plugin;

/**
 * Should we sync files (and use local files) or not?
 *
 * Another way to disable this functionality is to define constant. See more in FilesManager.
 */
class FileSyncOption extends BoolOption
{
    public function __construct()
    {
        parent::__construct();
        $this
            ->setName(Plugin::_NAME_ . '_file_sync');
    }
}

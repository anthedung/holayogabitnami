<?php
namespace Setka\Editor\Admin\Service\FilesManager;

use Setka\Editor\Admin\Options;

class DownloadListOfFilesFactory
{

    public static function create($tokenOption = null)
    {
        if(!$tokenOption) {
            $tokenOption = new Options\Token\Option();
        }
        return new DownloadListOfFiles($tokenOption);
    }
}

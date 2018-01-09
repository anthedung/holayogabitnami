<?php
namespace Setka\Editor\Admin\Service\FilesSync;

use Setka\Editor\Admin\Service\Filesystem\WordPressFilesystemFactory;

class FilesystemFactory {

	/**
	 * @return bool|FilesystemInterface false if Filesystem can't be created.
	 */
	public static function create() {
		$wpFilesystem = WordPressFilesystemFactory::create();

		$fs = new Filesystem();
		$fs->setFilesystem($wpFilesystem);

		return $fs;
	}
}

<?php
namespace Setka\Editor\Service\Config;

use Setka\Editor\Plugin;

class Files {

	public static function registerFilesDownloadingAttempts() {
		if(defined('SETKA_EDITOR_FILES_DOWNLOADING_ATTEMPTS'))
			return;

		define('SETKA_EDITOR_FILES_DOWNLOADING_ATTEMPTS', 3);
	}

	public static function getPath() {
		$config = wp_upload_dir();
		$path = path_join($config['basedir'], self::getSubPath());
		return apply_filters('setka_editor_files_path', $path);
	}

	public static function getSubPath() {
		return apply_filters('setka_editor_files_sub_path', Plugin::NAME);
	}

	public static function getUrl() {
		$config = wp_upload_dir();
		$url = $config['baseurl'];
		unset($config);

		$url .= '/' . ltrim(self::getSubPath(), '/');

		return $url;
	}
}

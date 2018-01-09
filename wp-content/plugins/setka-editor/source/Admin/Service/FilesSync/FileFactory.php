<?php
namespace Setka\Editor\Admin\Service\FilesSync;

use Setka\Editor\Entries\Meta\AttemptsToDownloadMeta;
use Setka\Editor\Entries\Meta\FileSubPathMeta;
use Setka\Editor\Entries\Meta\OriginUrlMeta;

class FileFactory {

	public static function create(\WP_Post $post, OriginUrlMeta $originUrlMeta = null, AttemptsToDownloadMeta $attemptsToDownloadMeta = null, FileSubPathMeta $fileSubPathMeta = null) {

		if(!$originUrlMeta)
			$originUrlMeta = new OriginUrlMeta();

		if(!$attemptsToDownloadMeta)
			$attemptsToDownloadMeta = new AttemptsToDownloadMeta();

		if(!$fileSubPathMeta)
			$fileSubPathMeta = new FileSubPathMeta();

		$file = new File($post, $originUrlMeta, $attemptsToDownloadMeta, $fileSubPathMeta);
		return $file;
	}
}

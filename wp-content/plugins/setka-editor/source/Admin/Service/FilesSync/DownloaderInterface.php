<?php
namespace Setka\Editor\Admin\Service\FilesSync;

use Setka\Editor\Admin\Service\FilesSync\Exceptions\ErrorDuringFileDownloadException;

interface DownloaderInterface {

	/**
	 * Download a single file and
	 *
	 * @throws ErrorDuringFileDownloadException If file was not downloaded.
	 *
	 * @param $url string An url to file which need to be downloaded.
	 *
	 * @return $this For chain calls.
	 */
	public function download($url);
}

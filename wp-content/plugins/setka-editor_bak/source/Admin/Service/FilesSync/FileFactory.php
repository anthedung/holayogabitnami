<?php
namespace Setka\Editor\Admin\Service\FilesSync;

use Setka\Editor\PostMetas\AttemptsToDownloadPostMeta;
use Setka\Editor\PostMetas\FileSubPathPostMeta;
use Setka\Editor\PostMetas\OriginUrlPostMeta;

class FileFactory
{
    /**
     * @param \WP_Post $post
     * @param OriginUrlPostMeta|null $originUrlMeta
     * @param AttemptsToDownloadPostMeta|null $attemptsToDownloadMeta
     * @param FileSubPathPostMeta|null $fileSubPathMeta
     * @param $downloadAttempts int
     *
     * @throws \Exception
     *
     * @return File
     */
    public static function create(
        \WP_Post $post,
        $downloadAttempts,
        OriginUrlPostMeta $originUrlMeta = null,
        AttemptsToDownloadPostMeta $attemptsToDownloadMeta = null,
        FileSubPathPostMeta $fileSubPathMeta = null
    ) {

        if(!$originUrlMeta) {
            $originUrlMeta = new OriginUrlPostMeta();
        }

        if(!$attemptsToDownloadMeta) {
            $attemptsToDownloadMeta = new AttemptsToDownloadPostMeta();
        }

        if(!$fileSubPathMeta) {
            $fileSubPathMeta = new FileSubPathPostMeta();
        }

        $file = new File($post, $originUrlMeta, $attemptsToDownloadMeta, $fileSubPathMeta, $downloadAttempts);
        return $file;
    }
}

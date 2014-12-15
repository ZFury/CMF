<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/11/14
 * Time: 5:44 PM
 */

namespace Media\Service;

class Video extends File
{
    const VIDEOS_PATH = "video/";

    public static function getDestination($path)
    {
        return preg_replace('/.[0-9]*\.((mp4))$/', '', $path);
    }

    /**
     * @param $id
     * @param $ext
     * @param bool $onlyPath
     * @return string
     * @throws \Exception
     */
    public static function videoPath($id, $ext, $onlyPath = false)//$onlyPath it's because we need another path when working with Original and when we are getting it
    {
        if ($onlyPath == false) {
            $path = self::PUBLIC_PATH . self::UPLOADS_PATH . self::VIDEOS_PATH;
        } else {
            $path = self::UPLOADS_PATH . self::VIDEOS_PATH;
        }

        return self::buildFilePath($id, $path, $ext);
    }
}

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
    const MP4_EXT = 'mp4';

    public static function getDestination($path)
    {
        return preg_replace('/.[0-9]*\.((mp4))$/', '', $path);
    }

    /**
     * @param $id
     * @param $ext
     * @param bool $from
     * @return string
     * @throws \Exception
     */
    public static function videoPath($id, $ext, $from = File::FROM_ROOT)//$onlyPath it's because we need another path when working with Original and when we are getting it
    {
        if ($from == File::FROM_ROOT) {
            $path = self::PUBLIC_PATH . self::UPLOADS_PATH . self::VIDEOS_PATH;
        } else {
            $path = self::UPLOADS_PATH . self::VIDEOS_PATH;
        }

        return self::buildFilePath($id, $path, $ext);
    }

    public function convertVideoToMp4(File $videoEntity, $bitrate = 300)
    {
        //With libav avconv installed
        $oldLocation = $videoEntity->getLocation();
        $videoEntity->setExtension(self::MP4_EXT);
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($videoEntity);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
        $newLocation = $videoEntity->getLocation();
        $this->executeConversion($oldLocation, $newLocation, $bitrate);

        return $videoEntity;
    }

    public function executeConversion($oldLocation, $newLocation, $bitrate = 300)
    {
        exec("avconv -i $oldLocation -strict experimental -b $bitrate" . "k -y $newLocation", $output, $return);
        if (isset($return) && 0 === $return) {
            return true;
        }

        return false;
    }
}

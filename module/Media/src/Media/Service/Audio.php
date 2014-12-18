<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/11/14
 * Time: 5:44 PM
 */

namespace Media\Service;

class Audio extends File
{
    const AUDIOS_PATH = "audio/";
    const MP3_EXT = 'mp3';

    public static function getDestination($path)
    {
        return preg_replace('/.[0-9]*\.((mp3))$/', '', $path);
    }

    /**
     * @param $id
     * @param $ext
     * @param bool $onlyPath
     * @return string
     * @throws \Exception
     */
    public static function audioPath($id, $ext, $onlyPath = false)//$onlyPath it's because we need another path when working with Original and when we are getting it
    {
        if ($onlyPath == false) {
            $path = self::PUBLIC_PATH . self::UPLOADS_PATH . self::AUDIOS_PATH;
        } else {
            $path = self::UPLOADS_PATH . self::AUDIOS_PATH;
        }

        return self::buildFilePath($id, $path, $ext);
    }

    public function convertAudioToMp3(\Media\Entity\File $audioEntity)
    {
        //With libav avconv installed
        $oldLocation = $audioEntity->getLocation();
        $audioEntity->setExtension(self::MP3_EXT);
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($audioEntity);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
        $newLocation = $audioEntity->getLocation();
        exec("avconv -i $oldLocation -c:a libmp3lame -b:a 320k $newLocation");

        return $audioEntity;
    }
}

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
     * @param bool $from
     * @return string
     * @throws \Exception
     */
    public static function audioPath($id, $ext, $from = \Media\Service\File::FROM_ROOT)//$onlyPath it's because we need another path when working with Original and when we are getting it
    {
        if ($from == \Media\Service\File::FROM_ROOT) {
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
        $this->executeConversion($oldLocation, $newLocation);

        return $audioEntity;
    }

    public function executeConversion($oldLocation, $newLocation)
    {
        exec("avconv -i $oldLocation -c:a libmp3lame -b:a 320k $newLocation", $output, $return);
        if (isset($return)) {
            return true;
        }

        return false;
    }
}

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

    /**
     * Returns a path to an audio file from root directory of a project (perfect choice for processing file)
     * or from public directory (perfect choice for displaying a link) using predefined constants in Media\Service\File
     *
     * @param $id (Id of an audio file)
     * @param $ext (Extension of a file)
     * @param bool $from (Can be FROM_ROOT or FROM_PUBLIC)
     * @return string
     * @throws \Exception
     */
    public static function audioPath($id, $ext, $from = File::FROM_ROOT)//$onlyPath it's because we need another path
    {//when working with Original and when we are getting it
        if ($from == File::FROM_ROOT) {
            $path = self::PUBLIC_PATH . self::UPLOADS_PATH . self::AUDIOS_PATH;
        } else {
            $path = DIRECTORY_SEPARATOR . self::UPLOADS_PATH . self::AUDIOS_PATH;
        }

        return self::buildFilePath($id, $path, $ext);
    }

    /**
     * Prepares all necessary data for converting audio file and calls "executeConversion" method
     *
     * @param File $audioEntity
     * @param string $newExtension
     * @return File
     */
    public function convertAudio(File $audioEntity, $newExtension = self::MP3_EXT)
    {
        //With libav avconv installed
        $oldLocation = $audioEntity->getLocation();
        $audioEntity->setExtension($newExtension);
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($audioEntity);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
        $newLocation = $audioEntity->getLocation();
        $this->executeConversion($oldLocation, $newLocation);

        return $audioEntity;
    }

    /**
     * Executes a command for conversion of a file
     *
     * @param $oldLocation
     * @param $newLocation
     * @return bool
     */
    public function executeConversion($oldLocation, $newLocation)
    {
        exec("avconv -i $oldLocation -c:a libmp3lame -b:a 320k -y $newLocation", $output, $return);
        if (isset($return) && 0 === $return) {
            return true;
        }

        return false;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/11/14
 * Time: 5:44 PM
 */

namespace Media\Service;

use Media\Entity\ObjectAudio;

class Audio extends File
{
    const AUDIOS_PATH = "audio/";

    public function writeObjectFileEntity($audio, $object)
    {
        $objectAudio = new ObjectAudio();
        $objectAudio->setAudio($audio);
        $objectAudio->setEntityName($object->getEntityName());
        $objectAudio->setObjectId($object->getId());
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($objectAudio);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

    public function writeFileEntity($data)
    {
        //Creating new image to get ID for building its path
        $audio = new \Media\Entity\Audio();
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($audio);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
        //Building path and creating directory. Then - moving
        $ext = $this->getExt($data['audio']['name']);
        $destination = $this->audioPath($audio->getId(), $ext);
        $this->moveFile($destination, $data['audio']);
        $audio->setExtension($ext);
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($audio);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();

        return $audio;
    }

    public function deleteFileEntity($fileId)
    {
        $file = $this->sm->get('doctrine.entitymanager.orm_default')->getRepository('Media\Entity\Audio')->findOneById($fileId);
        $this->sm->get('doctrine.entitymanager.orm_default')->remove($file);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

    public function deleteObjectFileEntity($fileId)
    {
        $objectFile = $this->sm->get('doctrine.entitymanager.orm_default')->getRepository('Media\Entity\ObjectAudio')->findOneByAudioId($fileId);
        $this->sm->get('doctrine.entitymanager.orm_default')->remove($objectFile);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

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
}

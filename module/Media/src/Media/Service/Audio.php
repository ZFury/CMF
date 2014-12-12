<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/11/14
 * Time: 5:44 PM
 */

namespace Media\Service;

use Zend\Filter\File\RenameUpload;
use Media\Entity\ObjectAudio;

class Audio
{
    const PATH = "/uploads/audio/";
    const PUBLIC_PATH = "public";
    const GETPATH = true;

    protected $sm;

    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param $data
     * @param $object
     * @return \Media\Entity\Audio
     */
    public function createAudio($data, $object)
    {
        //Creating new image to get ID for building its path
        $audio = new \Media\Entity\Audio();
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($audio);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
        //Building path and creating directory. Then - moving
        $ext = $this->getExt($data['audio']['name']);//??????????????????
        $destination = $this->audioPath($audio->getId(), $ext);
        $this->moveAudio($destination, $data['audio']);//??????????????????
        $audio->setExtension($ext);
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($audio);


        $objectAudio = new ObjectAudio();
        $objectAudio->setAudio($audio);
        $objectAudio->setEntityName($object->getEntityName());
        $objectAudio->setObjectId($object->getId());
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($objectAudio);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();

        return $audio;
    }

    /**
     * @param $audioId
     */
    public function deleteAudio($audioId)
    {
        $objectAudio = $this->sm->get('doctrine.entitymanager.orm_default')->getRepository('Media\Entity\ObjectAudio')->findOneByAudioId($audioId);
        $this->sm->get('doctrine.entitymanager.orm_default')->remove($objectAudio);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();

        $audio = $this->sm->get('doctrine.entitymanager.orm_default')->getRepository('Media\Entity\Audio')->findOneById($audioId);
        $this->sm->get('doctrine.entitymanager.orm_default')->remove($audio);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

    /**
     * @param $destination
     * @param $audio
     * @return array|string
     */
    public static function moveAudio($destination, $audio)
    {
        self::prepareDir($destination);
        $filter = new RenameUpload(
            array(
                "target" => $destination,
                'randomize' => false,
            )
        );

        return $filter->filter($audio);
    }

    /**
     * @param $path
     * @param int $mode
     * @return bool
     * @throws \Exception
     */
    public static function prepareDir($path, $mode = 0775)
    {
        $destination = preg_replace('/.[0-9]*\.((mp3))$/', '', $path);
        if (!is_dir($destination)) {
            if (self::prepareDir(dirname($destination), $mode)) {
                if (!is_writable(dirname($destination))) {
                    throw new \Exception('Directory ' . dirname($destination) . 'is not writable');
                }
                return mkdir($destination) && chmod($destination, $mode);
            }
        }

        return true;
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
            $path = self::PUBLIC_PATH . self::PATH;
        } else {
            $path = self::PATH;
        }

        return self::buildAudioPath($id, $path, $ext);
    }

    /**
     * @param $id
     * @param $path
     * @param $ext
     * @return string
     */
    public static function buildAudioPath($id, $path, $ext)
    {
        return rtrim($path, "/") . "/" . trim(self::buildPath($id, $ext), '/');
    }

    /**
     * @param $id
     * @param $ext
     * @return string
     */
    public static function buildPath($id, $ext)
    {
        $path = sprintf('%012d', $id);
        $explodedPath = str_split($path, 3);
        $temp = implode('/', $explodedPath);
        $finalPath = $temp . '.' . $ext;

        return $finalPath;
    }

    /**
     * @param $imageName
     * @return mixed
     */
    public static function getExt($imageName)
    {
        return preg_replace('(.*\.)', '', $imageName);
    }

    /**
     * @param $urlPart
     * @return string
     */
    public function getFullUrl($urlPart)
    {
        return $this->sm->get('ViewHelperManager')->get('ServerUrl')->__invoke() . $urlPart;
    }

    public function generateAudioUploadForm($module)
    {
        echo $this->sm->get('ViewHelperManager')->get('Partial')->__invoke('layout/file-upload/audio-upload-form.phtml');
        echo "<script>require(['" . $module . "']);</script>";
    }
}

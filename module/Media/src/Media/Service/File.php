<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/12/14
 * Time: 1:31 PM
 */

namespace Media\Service;

use Media\Entity\ObjectFile;
use Zend\Filter\File\RenameUpload;

class File
{
    const PUBLIC_PATH = "public";
    const UPLOADS_PATH = "/uploads/";
    const GETPATH = true;

    protected $sm;

    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param $path
     * @param int $mode
     * @return bool
     * @throws \Exception
     */
    public static function prepareDir($path, $mode = 0775)
    {
        $destination = self::getDestination($path);
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
     * @param $destination
     * @param $file
     * @return array|string
     */
    public function moveFile($destination, $file)
    {
        $this->prepareDir($destination);
        $filter = new RenameUpload(
            array(
                "target" => $destination,
                'randomize' => false,
            )
        );

        return $filter->filter($file);
    }

    public static function getDestination($path)
    {
        return preg_replace('/.[0-9]*\.((....)|(...))$/', '', $path);
    }

    /**
     * @param $urlPart
     * @return string
     */
    public function getFullUrl($urlPart)
    {
        return $this->sm->get('ViewHelperManager')->get('ServerUrl')->setPort(80)->__invoke() . $urlPart;
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
     * @param $id
     * @param $path
     * @param $ext
     * @return string
     */
    public static function buildFilePath($id, $path, $ext)
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
     * @param $fileId
     */
    public function deleteFile($fileId)
    {
        $this->deleteObjectFileEntity($fileId);
        $this->deleteFileEntity($fileId);
    }

    public function deleteFileEntity($fileId)
    {
        $file = $this->sm->get('doctrine.entitymanager.orm_default')->getRepository('Media\Entity\File')->findOneById($fileId);
        $this->sm->get('doctrine.entitymanager.orm_default')->remove($file);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

    public function deleteObjectFileEntity($fileId)
    {
        $objectFile = $this->sm->get('doctrine.entitymanager.orm_default')->getRepository('Media\Entity\ObjectFile')->findOneByFileId($fileId);
        $this->sm->get('doctrine.entitymanager.orm_default')->remove($objectFile);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

    public function createFile($data, $object)
    {
        $file = $this->writeFileEntity($data);
        $this->writeObjectFileEntity($file, $object);

        return $file;
    }

    public function writeObjectFileEntity($file, $object)
    {
        $objectFile = new ObjectFile();
        $objectFile->setFile($file);
        $objectFile->setEntityName($object->getEntityName());
        $objectFile->setObjectId($object->getId());
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($objectFile);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

    public function writeFileEntity($data)
    {
        //Creating new image to get ID for building its path
        $file = new \Media\Entity\File();
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($file);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
        //Building path and creating directory. Then - moving
        $ext = null;
        $destination = null;
        $type = null;
        switch (array_keys($data)[0]) {
            case \Media\Entity\File::AUDIO_FILETYPE:
                $ext = $this->getExt($data[\Media\Entity\File::AUDIO_FILETYPE]['name']);
                $destination = \Media\Service\Audio::audioPath($file->getId(), $ext);
                $type = \Media\Entity\File::AUDIO_FILETYPE;
                $this->moveFile($destination, $data[\Media\Entity\File::AUDIO_FILETYPE]);
                break;
            case \Media\Entity\File::VIDEO_FILETYPE:
                $ext = $this->getExt($data[\Media\Entity\File::VIDEO_FILETYPE]['name']);
                $destination = \Media\Service\Video::videoPath($file->getId(), $ext);
                $type = \Media\Entity\File::VIDEO_FILETYPE;
                $this->moveFile($destination, $data[\Media\Entity\File::VIDEO_FILETYPE]);
                break;
            case \Media\Entity\File::IMAGE_FILETYPE:
                $ext = $this->getExt($data[\Media\Entity\File::IMAGE_FILETYPE]['name']);
                $destination = \Media\Service\Image::imgPath(\Media\Service\Image::ORIGINAL, $file->getId(), $ext);

                $type = \Media\Entity\File::IMAGE_FILETYPE;
                $this->moveFile($destination, $data[\Media\Entity\File::IMAGE_FILETYPE]);
                break;
            default:
                break;
        }

        $file->setExtension($ext);
        $file->setType($type);
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($file);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();

        if ($file->getType() == \Media\Entity\File::VIDEO_FILETYPE &&
            $file->getExtension() !== \Media\Service\Video::MP4_EXT
        ) {
            $file = $this->sm->get('Media\Service\Video')->convertVideoToMp4($file);
        } elseif ($file->getType() == \Media\Entity\File::AUDIO_FILETYPE &&
            $file->getExtension() !== \Media\Service\Audio::MP3_EXT
        ) {
            $file = $this->sm->get('Media\Service\Audio')->convertAudioToMp3($file);
        }

        return $file;
    }

    public function generateFileUploadForm($module, $filetype)
    {
        echo $this->sm->get('ViewHelperManager')->get('Partial')->__invoke("layout/file-upload/file-upload-form.phtml", ['filetype' => $filetype]);
        echo "<script>require(['" . $module . "']);</script>";
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/12/14
 * Time: 1:31 PM
 */

namespace Media\Service;

use Media\Entity\File as FileEntity;
use Media\Entity\ObjectFile;
use Media\Form\FileUpload;
use Zend\Filter\File\RenameUpload;

class File
{
    const PUBLIC_PATH = "public";
    const UPLOADS_PATH = "/uploads/";
    const GETPATH = true;
    const FROM_PUBLIC = true;
    const FROM_ROOT = false;

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
        $file = $this->sm->get('doctrine.entitymanager.orm_default')->getRepository('Media\Entity\File')->find($fileId);
        $this->deleteObjectFileEntity($file);
        $this->deleteFileEntity($file);
    }

    public function deleteFileEntity(FileEntity $file)
    {
        $this->sm->get('doctrine.entitymanager.orm_default')->remove($file);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

    public function deleteObjectFileEntity(FileEntity $file)
    {
        $objectFile = $this->sm->get('doctrine.entitymanager.orm_default')->getRepository('Media\Entity\ObjectFile')->findOneByFileId($file->getId());
        $this->sm->get('doctrine.entitymanager.orm_default')->remove($objectFile);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

    public function createFile(FileUpload $form, $object)
    {
        $file = $this->writeFile($form);
        $this->associateFileWithObject($file, $object);

        return $file;
    }

    public function associateFileWithObject($file, $object)
    {
        $objectFile = new ObjectFile();
        $objectFile->setFile($file);
        $objectFile->setEntityName($object->getEntityName());
        $objectFile->setObjectId($object->getId());
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($objectFile);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

    public function writeFile(FileUpload $form)
    {
        //Creating new image to get ID for building its path
        $file = new FileEntity();
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($file);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
        //Building path and creating directory. Then - moving
        $ext = $this->getExt($form->getData()[$form->getFileType()]['name']);
        $destination = null;
        $type = $form->getFileType();
        switch ($type) {
            case FileEntity::AUDIO_FILETYPE:
                $destination = Audio::audioPath($file->getId(), $ext);
                break;
            case FileEntity::VIDEO_FILETYPE:
                $destination = Video::videoPath($file->getId(), $ext);
                break;
            case FileEntity::IMAGE_FILETYPE:
                $destination = Image::imgPath(Image::ORIGINAL, $file->getId(), $ext);
                break;
            default:
        }
        $this->moveFile($destination, $form->getData()[$form->getFileType()]);
        $file->setExtension($ext);
        $file->setType($type);
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($file);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();

        return $file;
    }

    public function convertFile(FileEntity $file)
    {
        if ($file->getType() == FileEntity::VIDEO_FILETYPE &&
            $file->getExtension() !== Video::MP4_EXT
        ) {
            $file = $this->sm->get('Media\Service\Video')->convertVideoToMp4($file);
        } elseif ($file->getType() == FileEntity::AUDIO_FILETYPE &&
            $file->getExtension() !== Audio::MP3_EXT
        ) {
            $file = $this->sm->get('Media\Service\Audio')->convertAudioToMp3($file);
        }

        return $file;
    }

    public function generateFileUploadForm($filetype)
    {
        echo $this->sm->get('ViewHelperManager')->get('Partial')->__invoke(
            "layout/default/partial/file-upload-form.phtml",
            ['filetype' => $filetype]
        );
    }
}

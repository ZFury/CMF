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
    const PUBLIC_PATH = "public/";
    const UPLOADS_PATH = "uploads/";
    const GETPATH = true;
    const FROM_PUBLIC = true;
    const FROM_ROOT = false;
    const DEFAULT_FILTER = false;

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $sm;

    /**
     * Construct
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sm
     */
    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    /**
     * Creates all directories that don't exist in a given path
     *
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
                    throw new \Exception('Directory ' . dirname($destination) . ' is not writable');
                }

                return mkdir($destination) && chmod($destination, $mode);
            }
        }

        return true;
    }

    /**
     * Returns the destination directory from a given path (given path usually has file in the end)
     *
     * @param $path
     * @return string
     */
    protected static function getDestination($path)
    {
        return preg_replace('/.[0-9]*\.((....)|(...))$/', '', $path);
    }

    /**
     * Generates full url from an url part e.g. You give "/module/controller/action/id", it returns
     * "www.test.com/module/controller/action/id"
     *
     * @param $urlPart
     * @return string
     */
    public function getFullUrl($urlPart)
    {
        return $this->sm->get('ViewHelperManager')->get('ServerUrl')->setPort(80)->__invoke() . $urlPart;
    }

    /**
     * Returns an extension of a file
     *
     * @param $fileName
     * @return string
     */
    protected static function getExt($fileName)
    {
        return preg_replace('(.*\.)', '', $fileName);
    }

    /**
     * Returns a path to a file
     *
     * @param $id
     * @param $path
     * @param $ext
     * @return string
     */
    protected static function buildFilePath($id, $path, $ext)
    {
        return rtrim($path, "/") . "/" . trim(self::buildPath($id, $ext), '/');
    }

    /**
     * Returns an end part of a file path (used in buildFilePath method)
     *
     * @param $id
     * @param $ext
     * @return string
     */
    private static function buildPath($id, $ext)
    {
        $path = sprintf('%012d', $id);
        $explodedPath = str_split($path, 3);
        $temp = implode('/', $explodedPath);
        $finalPath = $temp . '.' . $ext;

        return $finalPath;
    }

    /**
     * Deletes a file using sub-methods for deleting File entity and ObjectFile entity
     *
     * @param $fileId
     */
    public function deleteFile($fileId)
    {
        $file = $this->sm->get('doctrine.entitymanager.orm_default')->getRepository('Media\Entity\File')->find($fileId);
        $this->deleteObjectFileEntity($file);
        $this->deleteFileEntity($file);
    }

    /**
     * Deletes File entity
     *
     * @param FileEntity $file
     */
    public function deleteFileEntity(FileEntity $file)
    {
        $this->sm->get('doctrine.entitymanager.orm_default')->remove($file);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

    /**
     * Deletes ObjectFile entity
     *
     * @param FileEntity $file
     */
    public function deleteObjectFileEntity(FileEntity $file)
    {
        $objectFile = $this->sm->get('doctrine.entitymanager.orm_default')
            ->getRepository('Media\Entity\ObjectFile')->findOneByFileId($file->getId());
        $this->sm->get('doctrine.entitymanager.orm_default')->remove($objectFile);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

    /**
     * Creates file using sub-methods to write file and to associate it with an object
     *
     * @param FileUpload $form
     * @param $object
     * @return FileEntity
     */
    public function createFile(FileUpload $form, $object)
    {
        $file = $this->writeFile($form);
        $this->associateFileWithObject($file, $object);

        return $file;
    }

    /**
     * Makes an association between a created file and an entity. It means, that the object file be the owner
     * of the file
     *
     * @param FileEntity $file
     * @param $object
     */
    public function associateFileWithObject(FileEntity $file, $object)
    {
        $objectFile = new ObjectFile();
        $objectFile->setFile($file);
        $objectFile->setEntityName($object->getEntityName());
        $objectFile->setObjectId($object->getId());
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($objectFile);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

    /**
     * Writes file to a DB and moves it to an appropriate path
     *
     * @param FileUpload $form
     * @return FileEntity
     * @throws \Exception
     */
    public function writeFile(FileUpload $form)
    {
        $ext = $this->getExt($form->getData()[$form->getFileType()]['name']);
        $type = $form->getFileType();
        //Creating new image to get ID for building its path
        $file = new FileEntity();
        $file->setExtension($ext);
        $file->setType($type);
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($file);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
        //Building path and creating directory. Then - moving
        $destination = null;
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
        $this->prepareDir($destination);
        $this->moveFile($destination, $form->getData()[$type]);

        return $file;
    }

    /**
     * Moves a file to a directory (this method is used in writeFile method
     *
     * @param $destination
     * @param $file
     * @return array|string
     */
    private function moveFile($destination, $file)
    {
        /** @var \Zend\Filter\File\RenameUpload $filter */
        $filter = $this->sm->get('Zend\Filter\File\RenameUpload');
        $filter->setTarget($destination)
            ->setRandomize(false);

        return $filter->filter($file);
    }

    /**
     * Converts file according to its type
     *
     * @param FileEntity $file
     * @return FileEntity
     */
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

    /**
     * Generates upload form according to a file type
     *
     * @param string $filetype
     */
    public function generateFileUploadForm($filetype)
    {
        echo $this->sm->get('ViewHelperManager')->get('Partial')->__invoke(
            "layout/default/partial/file-upload-form.phtml",
            ['filetype' => $filetype]
        );
    }
}

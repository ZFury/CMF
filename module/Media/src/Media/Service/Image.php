<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 12:25 PM
 */

namespace Media\Service;

use Zend\Filter\File\RenameUpload;
use Media\Entity\ObjectImage;

class Image
{
    const PATH = "/uploads/images/";
    const PUBLIC_PATH = "public";
    const ORIGINAL = 3;
    const BIG_THUMB = 2;
    const SMALL_THUMB = 1;
    const B_THUMB_WIDTH = 400;
    const B_THUMB_HEIGHT = 400;
    const S_THUMB_WIDTH = 150;
    const S_THUMB_HEIGHT = 150;
    const GETPATH = true;

    protected $sm;

    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param $data
     * @return \Media\Entity\Image
     */
    public function createImage($data, $object)
    {
        //Creating new image to get ID for building its path
        $image = new \Media\Entity\Image();
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($image);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
        //Building path and creating directory. Then - moving
        $ext = \Media\Service\Image::getExt($data['image']['name']);
        $destination = \Media\Service\Image::imgPath(\Media\Service\Image::ORIGINAL, $image->getId(), $ext);
        \Media\Service\Image::moveImage($destination, $data['image']);
        $image->setExtension($ext);
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($image);

        $objectImage = new ObjectImage();
        $objectImage->setImage($image);
        $objectImage->setEntityName($object->getEntityName());
        $objectImage->setObjectId($object->getId());
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($objectImage);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();

        return $image;
    }

    /**
     * @param $imageId
     */
    public function deleteImage($imageId)
    {
        $objectImage = $this->sm->get('doctrine.entitymanager.orm_default')->getRepository('Media\Entity\ObjectImage')->findOneByImageId($imageId);
        $this->sm->get('doctrine.entitymanager.orm_default')->remove($objectImage);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();

        $image = $this->sm->get('doctrine.entitymanager.orm_default')->getRepository('Media\Entity\Image')->findOneById($imageId);
        $this->sm->get('doctrine.entitymanager.orm_default')->remove($image);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

    /**
     * @param $destination
     * @param $image
     * @return array|string
     */
    public static function moveImage($destination, $image)
    {
        self::prepareDir($destination);
        $filter = new RenameUpload(
            array(
                "target" => $destination,
                'randomize' => false,
            )
        );

        return $filter->filter($image);
    }

    /**
     * @param $path
     * @param int $mode
     * @return bool
     * @throws \Exception
     */
    public static function prepareDir($path, $mode = 0775)
    {
        $destination = preg_replace('/.[0-9]*\.((jpeg)|(jpg)|(png))$/', '', $path);
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
     * @param $type
     * @param $id
     * @param $ext
     * @param bool $onlyPath
     * @return string
     * @throws \Exception
     */
    public static function imgPath($type, $id, $ext, $onlyPath = false)//$onlyPath it's because we need another path when working with Original and when we are getting it
    {
        if (self::ORIGINAL == $type) {
            if ($onlyPath == false) {
                $path = self::PUBLIC_PATH . self::PATH . "original";
            } else {
                $path = self::PATH . "original";
            }


        } else {
            $size = self::sizeByType($type);
            if (empty($size)) {
                throw new \Exception('Unsupported size');
            }
            $path = self::PATH . $size['width'] . 'x' . $size['height'];
        }

        return self::buildImagePath($id, $path, $ext);
    }

    /**
     * @param $id
     * @param $path
     * @param $ext
     * @return string
     */
    public static function buildImagePath($id, $path, $ext)
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
     * @param $type
     * @return array
     */
    protected static function sizeByType($type)
    {
        switch ($type) {
            case self::BIG_THUMB:
                return array('width' => self::B_THUMB_WIDTH, 'height' => self::B_THUMB_HEIGHT);
            case self::SMALL_THUMB:
                return array('width' => self::S_THUMB_WIDTH, 'height' => self::S_THUMB_HEIGHT);
        }
    }

    /**
     * @param $urlPart
     * @return string
     */
    public function getFullUrl($urlPart)
    {
        return $this->sm->get('ViewHelperManager')->get('ServerUrl')->__invoke() . $urlPart;
    }

    public function generateImageUploadForm($module)
    {
        echo $this->sm->get('ViewHelperManager')->get('Partial')->__invoke('layout/file-upload/image-upload-form.phtml');
        echo "<script>require(['" . $module . "']);</script>";
    }
}

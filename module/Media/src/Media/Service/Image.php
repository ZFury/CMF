<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 12:25 PM
 */

namespace Media\Service;

use Media\Entity\ObjectImage;

class Image extends File
{
    const IMAGES_PATH ="images/";

    const ORIGINAL = 3;
    const BIG_THUMB = 2;
    const SMALL_THUMB = 1;
    const B_THUMB_WIDTH = 400;
    const B_THUMB_HEIGHT = 400;
    const S_THUMB_WIDTH = 150;
    const S_THUMB_HEIGHT = 150;

    public function writeObjectFileEntity($image, $object)
    {
        $objectImage = new ObjectImage();
        $objectImage->setImage($image);
        $objectImage->setEntityName($object->getEntityName());
        $objectImage->setObjectId($object->getId());
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($objectImage);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
    }

    public function writeFileEntity($data)
    {
        //Creating new image to get ID for building its path
        $image = new \Media\Entity\Image();
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($image);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
        //Building path and creating directory. Then - moving
        $ext = $this->getExt($data['image']['name']);
        $destination = $this->imgPath(self::ORIGINAL, $image->getId(), $ext);
        $this->moveFile($destination, $data['image']);
        $image->setExtension($ext);
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($image);
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


    public static function getDestination($path)
    {
        return preg_replace('/.[0-9]*\.((jpeg)|(jpg)|(png))$/', '', $path);
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
                $path = self::PUBLIC_PATH . self::UPLOADS_PATH . "original/";
            } else {
                $path = self::UPLOADS_PATH . "original/";
            }


        } else {
            $size = self::sizeByType($type);
            if (empty($size)) {
                throw new \Exception('Unsupported size');
            }
            $path = self::UPLOADS_PATH . self::IMAGES_PATH . $size['width'] . 'x' . $size['height'];
        }

        return self::buildFilePath($id, $path, $ext);
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
}

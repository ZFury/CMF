<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 12:25 PM
 */

namespace Media\Service;

use Zend\Filter\File\RenameUpload;
use Media\Entity\Image;

class ImageService
{
    const PATH = "/uploads/media/images/";
    const PUBLIC_PATH = "public/";
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
     * @param $obj
     * @param bool $api
     * @return array
     */
    public function createImage($data, $obj, $api = false)
    {
        //Creating new image to get ID for building its path
        $image = new Image();
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($image);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
        //Building path and creating directory. Then - moving
        $ext = ImageService::getExt($data['image']['name']);
        $destination = ImageService::imgPath(ImageService::ORIGINAL, $image->getId(), $ext);
        ImageService::moveImage($destination, $data['image']);
        //Saving original image extension in database and adding to it our User
        $image->setExtension($ext);
        $deleteUrl = null;
        //just trying
        switch (get_class($obj)) {
            case 'User\Entity\User':
                $image->addUser($obj);
                $deleteUrl = '/user/delete-image/' . $image->getId();

                break;
            case 'User\Entity\Commodity':
                $image->addCommodity($obj);
                $deleteUrl = '/commodity-management/delete-image/' . $image->getId();
                break;
            default:
                break;
        }

        $this->sm->get('doctrine.entitymanager.orm_default')->persist($image);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
        //For blueimp
        if (true === $api) {
            $dataForJson = array(
                'images' => array(
                    array(
                        'id' => $image->getId(),
                        'url_original' => $this->getFullUrl(
                            $this->imgPath(
                                ImageService::ORIGINAL,
                                $image->getId(),
                                $image->getExtension(),
                                true
                            )
                        ),
                        'url_thumb' => $this->getFullUrl($image->getThumb()),
                    )
                )
            );
        } else {
            $dataForJson =  ['files' =>[
                array(
                    'url' => $image->getThumb(),
                    'thumbnailUrl' => $image->getThumb(),
                    'name' => '',
                    'type' => 'image/jpeg',
                    'size' => '',
                    'deleteUrl' => $deleteUrl,
                    'deleteType' => 'POST',
                )]];
        }

        return $dataForJson;
    }

    /**
     * @param $destination
     * @param $image
     * @return array|string
     */
    public static function moveImage($destination, $image)
    {
        self::prepareDir($destination);
        $filter = new RenameUpload(array(
            "target" => $destination,
            'randomize' => false,
        ));
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
     * @param $type
     * @param $id
     * @param $ext
     * @param bool $onlyPath
     * @return string
     * @throws \Exception
     */
    public static function imgPath(
        $type,
        $id,
        $ext,
        $onlyPath = false
    ) //$onlyPath it's because we need another path when working with Original and when we are getting it
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
     * @param \Doctrine\ORM\PersistentCollection $images
     * @param $obj
     * @return array
     */
    public function displayImages(\Doctrine\ORM\PersistentCollection $images, $obj)
    {
        switch (get_class($obj)) {
            case 'User\Entity\User':
                $deleteUrl = '/user/delete-image/';

                break;
            case 'User\Entity\Commodity':
                $deleteUrl = '/commodity-management/delete-image/';
                break;
            default:
                break;
        }

        $externalArray = array();
        foreach ($images as $image) {
            $innerArray = array(
                'url' => $image->getThumb(),
                'thumbnailUrl' => $image->getThumb(),
                'deleteUrl' => $deleteUrl . $image->getId(),
                'deleteType' => 'POST',
                'id' => $image->getId(),
            );
            array_push($externalArray, $innerArray);
        }

        return $externalArray;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $avatars
     * @return array|null
     */
    public function getAvatars(\Doctrine\ORM\PersistentCollection $avatars)
    {
        $avatarNames = null;
        if (count($avatars) >= 1) {
            $avatarNames = array();
            foreach ($avatars as $avatarPrev) {
                $avaPrevExt = $avatarPrev->getExtension();
                $avaPrevId = $avatarPrev->getId();
                $avatarPrev = ImageService::imgPath(
                    ImageService::ORIGINAL,
                    $avaPrevId,
                    $avaPrevExt,
                    ImageService::GETPATH
                ); //It's because we need a different path if we want image to be showed
                array_push($avatarNames, $avatarPrev);
            }
        }

        return $avatarNames;
    }

    /**
     * @param $urlPart
     * @return string
     */
    public function getFullUrl($urlPart)
    {
        return $this->sm->get('ViewHelperManager')->get('ServerUrl')->__invoke() . $urlPart;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $images
     * @return array
     */
    public function getImagesInfo(\Doctrine\ORM\PersistentCollection $images)
    {
        $extArr = array();
        foreach ($images as $image) {
            $urlOriginal = $this->getFullUrl(
                $this->imgPath(
                    ImageService::ORIGINAL,
                    $image->getId(),
                    $image->getExtension(),
                    true
                )
            );
            $urlThumb = $this->getFullUrl($image->getThumb());
            array_push(
                $extArr,
                array(
                    'id' => $image->getId(),
                    'url_original' => $urlOriginal,
                    'url_thumb' => $urlThumb,
                )
            );
        }
        return $extArr;
    }

    //////////////////////////////////////////////////////////
    ////////////////////HELPERS///////////////////////////////
    //////////////////////////////////////////////////////////
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
}

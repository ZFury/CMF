<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/4/14
 * Time: 1:44 PM
 */

namespace Media\Service;

class Blueimp
{
    protected $sm;

    public function __construct($sm)
    {
        $this->sm = $sm;
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
                $avatarPrev = \Media\Service\Image::imgPath(
                    \Media\Service\Image::ORIGINAL,
                    $avaPrevId,
                    $avaPrevExt,
                    \Media\Service\Image::GETPATH
                ); //It's because we need a different path if we want image to be showed
                array_push($avatarNames, $avatarPrev);
            }
        }

        return $avatarNames;
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
        $image = new \Media\Entity\Image();
        $this->sm->get('doctrine.entitymanager.orm_default')->persist($image);
        $this->sm->get('doctrine.entitymanager.orm_default')->flush();
        //Building path and creating directory. Then - moving
        $ext = \Media\Service\Image::getExt($data['image']['name']);
        $destination = \Media\Service\Image::imgPath(\Media\Service\Image::ORIGINAL, $image->getId(), $ext);
        \Media\Service\Image::moveImage($destination, $data['image']);
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
                                \Media\Service\Image::ORIGINAL,
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
}
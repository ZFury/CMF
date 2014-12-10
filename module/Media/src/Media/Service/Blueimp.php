<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/10/14
 * Time: 11:16 AM
 */
namespace Media\Service;

use Media\Service\Image;
use Zend\View\Model\JsonModel;

class Blueimp
{
    protected $sm;

    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    public function getImageJson($image, $deleteUrl)
    {
        $imageService = $this->sm->get('Media\Service\Image');

        return [
            'url' => $imageService->getFullUrl($image->getUrlPart()),
            'thumbnailUrl' => $imageService->getFullUrl($image->getThumb()),
            'name' => '',
            'type' => 'image/jpeg',
            'size' => '',
            'deleteUrl' => $deleteUrl,
            'deleteType' => 'POST',
        ];
    }

    public function displayUploadedImage($image, $deleteUrl)
    {
        return [
          'files' => [
              $this->getImageJson($image, $deleteUrl)
          ]
        ];
    }

    public function displayUploadedImages($images, $deleteUrls)
    {
        $imagesJson = array();
        foreach ($images as $image) {
            foreach ($deleteUrls as $deleteUrl) {
                if ($deleteUrl['id'] == $image->getId()) {
                    array_push($imagesJson, $this->getImageJson($image, $deleteUrl['deleteUrl']));
                }
            }
        }

        return [ 'files' =>  $imagesJson ];
    }

    public function deleteImageJson($imageId)
    {
        return new JsonModel([
            'files' =>[ $imageId => 'true' ]
        ]);
    }

    public function deleteImagesUrl($images)
    {
        $deleteUrls = [];
        foreach ($images as $image) {
            array_push($deleteUrls, [
                'id' => $image->getId(),
                'deleteUrl' => $this->getDeleteUrl($image)
            ]);
        }

        return $deleteUrls;
    }
}
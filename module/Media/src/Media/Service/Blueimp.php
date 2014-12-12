<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/10/14
 * Time: 11:16 AM
 */
namespace Media\Service;

use Zend\View\Model\JsonModel;

class Blueimp
{
    protected $sm;

    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param $image
     * @param $deleteUrl
     * @return array
     */
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

    /**
     * @param $audio
     * @param $deleteUrl
     * @return array
     */
    public function getAudioJson($audio, $deleteUrl)
    {
        $audioService = $this->sm->get('Media\Service\Audio');

        return [
            'url' => $audioService->getFullUrl($audio->getUrlPart()),
            'thumbnailUrl' => $audioService->getFullUrl($audio->getUrlPart()),
            'name' => '',
            'type' => 'audio/mp3',
            'size' => '',
            'deleteUrl' => $deleteUrl,
            'deleteType' => 'POST',
        ];
    }

    /**
     * @param $image
     * @param $deleteUrl
     * @return array
     */
    public function displayUploadedImage($image, $deleteUrl)
    {
        return [
          'files' => [
              $this->getImageJson($image, $deleteUrl)
          ]
        ];
    }

    /**
     * @param $audio
     * @param $deleteUrl
     * @return array
     */
    public function displayUploadedAudio($audio, $deleteUrl)
    {
        return [
            'files' => [
                $this->getAudioJson($audio, $deleteUrl)
            ]
        ];
    }

    /**
     * @param $images
     * @param $deleteUrls
     * @return array
     */
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

    /**
     * @param $audios
     * @param $deleteUrls
     * @return array
     */
    public function displayUploadedAudios($audios, $deleteUrls)
    {
        $imagesJson = array();
        foreach ($audios as $audio) {
            foreach ($deleteUrls as $deleteUrl) {
                if ($deleteUrl['id'] == $audio->getId()) {
                    array_push($imagesJson, $this->getAudioJson($audio, $deleteUrl['deleteUrl']));
                }
            }
        }

        return [ 'files' =>  $imagesJson ];
    }

    /**
     * @param $imageId
     * @return JsonModel
     */
    public function deleteImageJson($imageId)
    {
        return new JsonModel([
            'files' =>[ $imageId => 'true' ]
        ]);
    }

    /**
     * @param $audioId
     * @return JsonModel
     */
    public function deleteAudioJson($audioId)
    {
        return new JsonModel([
            'files' =>[ $audioId => 'true' ]
        ]);
    }
}

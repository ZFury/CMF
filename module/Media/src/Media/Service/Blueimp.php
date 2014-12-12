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
     * @param $file
     * @param $deleteUrl
     * @return array
     */
    public function getFileJson($file, $deleteUrl)
    {
        $fileService = null;
        $thumbnailUrl = null;
        $type = null;
        switch (get_class($file)) {
            case \Media\Entity\File::IMAGE_CLASSNAME:
                $fileService = $this->sm->get('Media\Service\Image');
                $thumbnailUrl = $fileService->getFullUrl($file->getThumb());
                $type = 'image/jpeg';
                break;
            case \Media\Entity\File::AUDIO_CLASSNAME:
                $fileService = $this->sm->get('Media\Service\Audio');
                $thumbnailUrl = $fileService->getFullUrl($file->getUrlPart());
                $type = 'audio/mp3';
                break;
            default:
                break;
        }

        return [
            'url' => $fileService->getFullUrl($file->getUrlPart()),
            'thumbnailUrl' => $thumbnailUrl,
            'name' => '',
            'type' => $type,
            'size' => '',
            'deleteUrl' => $deleteUrl,
            'deleteType' => 'POST',
        ];
    }

    /**
     * @param $file
     * @param $deleteUrl
     * @return array
     */
    public function displayUploadedFile($file, $deleteUrl)
    {
        return ['files' => [ $this->getFileJson($file, $deleteUrl) ]];
    }


    /**
     * @param $files
     * @param $deleteUrls
     * @return array
     */
    public function displayUploadedFiles($files, $deleteUrls)
    {
        $filesJson = array();
        foreach ($files as $file) {
            foreach ($deleteUrls as $deleteUrl) {
                if ($deleteUrl['id'] == $file->getId()) {
                    array_push($filesJson, $this->getFileJson($file, $deleteUrl['deleteUrl']));
                }
            }
        }

        return [ 'files' =>  $filesJson ];
    }

    /**
     * @param $fileId
     * @return JsonModel
     */
    public function deleteFileJson($fileId)
    {
        return new JsonModel([
            'files' =>[ $fileId => 'true' ]
        ]);
    }
}

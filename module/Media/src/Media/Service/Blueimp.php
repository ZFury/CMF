<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/10/14
 * Time: 11:16 AM
 */
namespace Media\Service;

use Zend\View\Model\JsonModel;
use Media\Entity\File;

class Blueimp
{
    protected $sm;

    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param File $file
     * @param $deleteUrl
     * @return array
     */
    public function getFileJson(File $file, $deleteUrl)
    {
        $fileService = $this->sm->get('Media\Service\File');
        $thumbnailUrl = null;
        $type = null;
        switch ($file->getType()) {
            case File::IMAGE_FILETYPE:
                $thumbnailUrl = $fileService->getFullUrl($file->getThumb());
                $type = 'image/jpeg';
                break;
            case File::AUDIO_FILETYPE:
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
     * @param File $file
     * @param string $deleteUrl
     * @return array
     */
    public function displayUploadedFile(File $file, $deleteUrl)
    {
        return ['files' => [ $this->getFileJson($file, $deleteUrl) ]];
    }

    /**
     * @param array $files
     * @param array $deleteUrls
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
     * @param integer $fileId
     * @return JsonModel
     */
    public function deleteFileJson($fileId)
    {
        return new JsonModel([
            'files' => []
        ]);
    }
}

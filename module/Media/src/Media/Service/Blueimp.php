<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/10/14
 * Time: 11:16 AM
 */
namespace Media\Service;

use Zend\View\Model\JsonModel;
use Media\Entity\File as FileEntity;

class Blueimp
{
    protected $sm;

    /**
     * @param $sm
     */
    public function __construct($sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param FileEntity $file
     * @param $deleteUrl
     * @return array
     */
    public function getFileJson(FileEntity $file, $deleteUrl)
    {
        /** @var \Media\Service\File  $fileService */
        $fileService = $this->sm->get('Media\Service\File');
        $thumbnailUrl = null;
        $type = null;
        switch ($file->getType()) {
            case FileEntity::IMAGE_FILETYPE:
                $thumbnailUrl = $fileService->getFullUrl($file->getThumb());
                $type = 'image/jpeg';
                break;
            case FileEntity::AUDIO_FILETYPE:
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
            'deleteType' => 'DELETE',
        ];
    }

    /**
     * @param $file
     * @param $mask
     * @return array
     */
    public function displayUploadedFile(FileEntity $file, $mask)
    {
        return ['files' => [
            $this->getFileJson(
                $file,
                $this->sm->get('ViewHelperManager')->get('ServerUrl')->setPort(80)
                ->__invoke() . $mask . '?fileId=' . $file->getId()
            )
        ]];
    }

    /**
     * @param array $files
     * @param $mask
     * @return array
     */
    public function displayUploadedFiles(array $files, $mask)
    {
        $filesJson = array();
        foreach ($files as $file) {
            array_push(
                $filesJson,
                $this->getFileJson(
                    $file,
                    $this->sm->get('ViewHelperManager')->get('ServerUrl')->setPort(80)
                        ->__invoke() . $mask . '?fileId=' . $file->getId()
                )
            );

        }

        return [ 'files' =>  $filesJson ];
    }

    /**
     * @return JsonModel
     */
    public function deleteFileJson()
    {
        return new JsonModel([
            'files' =>[]
        ]);
    }
}

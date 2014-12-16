<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/10/14
 * Time: 6:13 PM
 */

namespace Media\Interfce;

interface ImageUploaderInterface
{
    /**
     * This method will receive POST\GET requests. In case of a POST request,
     * it must upload an video and return appropriate JSON using Blueimp service.
     * In case of a GET request it must return appropriate JSON that contains all videos
     * using also Blueimp service
     */
    public function startImageUploadAction();

    /**
     * This method must contain two strings. One actually deletes video from DB using File service and
     * another one returns appropriate Json to the view. So you need to write:
     * $fileService->deleteFile($this->getEvent()->getRouteMatch()->getParam('id'));
     * return $blueimpService->deleteFileJson($this->getEvent()->getRouteMatch()->getParam('id'));
     */
    public function deleteImageAction();

    /**
     * This method must return a full url of deleting an image, using File service. For example you can do this
     * like that:
     * $url = $this->serviceLocator->get('ViewHelperManager')->get('url');
     * return $fileService->getFullUrl($url('test/default', [
     *      'controller' => 'image',
     *      'action' => 'delete',
     *      'id' => $video->getId()
     * ]));
     * @param $image
     * @return mixed
     */
    public function getDeleteImageUrl($image);

    /**
     * This method must return an array of urls for deleting using method deleteFilesUrl($files) of Blueimp service
     * @param $images
     * @return mixed
     */
    public function getDeleteImageUrls($images);
}

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
     * This method must create form: new ImageUpload('upload-image')
     * and return it into the view
     */
    public function uploadImageAction();

    /**
     * This method will receive POST\GET requests. In case of a POST request,
     * it must upload an image and return appropriate JSON using Blueimp service.
     * In case of a GET request it must return appropriate JSON that contains all images
     * using also Blueimp service
     */
    public function startUploadAction();

    /**
     * This method must contain two strings. One actually deletes image from DB using Image service and
     * another one returns appropriate Json to the view. So you need to write:
     * $imageService->deleteImage($this->getEvent()->getRouteMatch()->getParam('id'));
     * return $blueimpService->deleteImageJson($this->getEvent()->getRouteMatch()->getParam('id'));
     */
    public function deleteImageAction();

    /**
     * This method must return a full url of deleting an image, using Image service. For example you can do this
     * like that:
     * $url = $this->serviceLocator->get('ViewHelperManager')->get('url');
     * return $imageService->getFullUrl($url('test/default', [
     *      'controller' => 'image',
     *      'action' => 'delete',
     *      'id' => $image->getId()
     * ]));
     * @param $image
     * @return mixed
     */
    public function getDeleteUrl($image);

    /**
     * This method must return an array of urls for deleting using method deleteImagesUrl($images) of Blueimp service
     * @param $images
     * @return mixed
     */
    public function getDeleteUrls($images);
}

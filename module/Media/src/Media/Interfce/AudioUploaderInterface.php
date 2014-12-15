<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/10/14
 * Time: 6:13 PM
 */

namespace Media\Interfce;

interface AudioUploaderInterface
{
    /**
     * This method will receive POST\GET requests. In case of a POST request,
     * it must upload an audio and return appropriate JSON using Blueimp service.
     * In case of a GET request it must return appropriate JSON that contains all audios
     * using also Blueimp service
     */
    public function startUploadAction();

    /**
     * This method must contain two strings. One actually deletes image from DB using Audio service and
     * another one returns appropriate Json to the view. So you need to write:
     * $audioService->deleteAudio($this->getEvent()->getRouteMatch()->getParam('id'));
     * return $blueimpService->deleteAudioJson($this->getEvent()->getRouteMatch()->getParam('id'));
     */
    public function deleteAudioAction();

    /**
     * This method must return a full url of deleting an image, using Audio service. For example you can do this
     * like that:
     * $url = $this->serviceLocator->get('ViewHelperManager')->get('url');
     * return $imageService->getFullUrl($url('test/default', [
     *      'controller' => 'audio',
     *      'action' => 'delete',
     *      'id' => $audio->getId()
     * ]));
     * @param $audio
     * @return mixed
     */
    public function getDeleteUrl($audio);

    /**
     * This method must return an array of urls for deleting using method deleteAudiosUrl($audios) of Blueimp service
     * @param $audios
     * @return mixed
     */
    public function getDeleteUrls($audios);
}

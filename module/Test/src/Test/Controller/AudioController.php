<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 11:09 AM
 */

namespace Test\Controller;

use Media\Service\Audio;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Media\Form\AudioUpload;
use Media\Form\Filter\AudioUploadInputFilter;
use Media\Interfce\AudioUploaderInterface;

class AudioController extends AbstractActionController implements AudioUploaderInterface
{
    public function uploadAudioAction()
    {
        $form = new AudioUpload('upload-audio');
        $audioService = new Audio($this->getServiceLocator());
        return new ViewModel(['form' => $form, 'audioService' => $audioService]);
    }

    /**
     * Advanced avatar uploader Blueimp UI
     */
    public function startUploadAction()
    {

        $user = $this->identity()->getUser();
        $audioService = $this->getServiceLocator()->get('Media\Service\Audio');
        $blueimpService = $this->getServiceLocator()->get('Media\Service\Blueimp');
        if ($this->getRequest()->isPost()) {
            $form = new AudioUpload('upload-image');
            $inputFilter = new AudioUploadInputFilter();
            $form->setInputFilter($inputFilter->getInputFilter());

            $request = $this->getRequest();
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->beginTransaction();
            $form->setData($post);

            if ($form->isValid()) {
                $data = $form->getData();
                $audio = $audioService->createAudio($data, $this->identity()->getUser());
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->commit();
                $dataForJson = $blueimpService->displayUploadedAudio($audio, $this->getDeleteUrl($audio));
            } else {
                $messages = $form->getMessages();
                $messages = array_shift($messages);
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->rollBack();
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->close();

                $dataForJson = [ 'files' => [
                        [
                            'name' => $form->get('audio')->getValue()['name'],
                            'error' => array_shift($messages)
                        ]
                ]];
            }
        } else {
            $dataForJson = $blueimpService->displayUploadedAudios(
                $user->getAudios(),
                $this->getDeleteUrls($user->getAudios())
            );
        }

        return new JsonModel($dataForJson);
    }

    public function deleteAudioAction()
    {
        $this->getServiceLocator()->get('Media\Service\Audio')
            ->deleteAudio($this->getEvent()->getRouteMatch()->getParam('id'));
        return $this->getServiceLocator()->get('Media\Service\Blueimp')
            ->deleteImageJson($this->getEvent()->getRouteMatch()->getParam('id'));
    }

    public function getDeleteUrl($audio)
    {
        $url = $this->serviceLocator->get('ViewHelperManager')->get('url');
        $audioService = $this->getServiceLocator()->get('Media\Service\Audio');
        return $audioService->getFullUrl($url('test/default', [
            'controller' => 'audio',
            'action' => 'delete',
            'id' => $audio->getId()
        ]));
    }

    public function getDeleteUrls($audios)
    {
        $deleteUrls = [];
        foreach ($audios as $audio) {
            array_push($deleteUrls, [
                'id' => $audio->getId(),
                'deleteUrl' => $this->getDeleteUrl($audio)
            ]);
        }

        return $deleteUrls;
    }
}

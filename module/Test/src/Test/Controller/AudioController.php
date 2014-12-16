<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 11:09 AM
 */

namespace Test\Controller;

use Media\Service\Audio;
use Media\Service\File;
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
        $fileService = new File($this->getServiceLocator());
        return new ViewModel(['fileService' => $fileService, 'module'=> 'audio', 'type' => \Media\Entity\File::AUDIO_FILETYPE]);
    }

    /**
     * Advanced avatar uploader Blueimp UI
     */
    public function startAudioUploadAction()
    {
        $user = $this->identity()->getUser();
        $fileService = $this->getServiceLocator()->get('Media\Service\File');
        $blueimpService = $this->getServiceLocator()->get('Media\Service\Blueimp');
        if ($this->getRequest()->isPost()) {
            $form = new AudioUpload('upload-audio');
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
                $audio = $fileService->createFile($data, $this->identity()->getUser());
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->commit();
                $dataForJson = $blueimpService->displayUploadedFile($audio, $this->getDeleteAudioUrl($audio));
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
            $dataForJson = $blueimpService->displayUploadedFiles(
                $user->getAudios(),
                $this->getDeleteAudioUrls($user->getAudios())
            );
        }

        return new JsonModel($dataForJson);
    }

    public function deleteAudioAction()
    {
        $this->getServiceLocator()->get('Media\Service\File')
            ->deleteFile($this->getEvent()->getRouteMatch()->getParam('id'));
        return $this->getServiceLocator()->get('Media\Service\Blueimp')
            ->deleteFileJson($this->getEvent()->getRouteMatch()->getParam('id'));
    }

    public function getDeleteAudioUrl($audio)
    {
        $url = $this->serviceLocator->get('ViewHelperManager')->get('url');
        $fileService = $this->getServiceLocator()->get('Media\Service\File');
        return $fileService->getFullUrl($url('test/default', [
            'controller' => 'audio',
            'action' => 'delete-audio',
            'id' => $audio->getId()
        ]));
    }

    public function getDeleteAudioUrls($audios)
    {
        $deleteUrls = [];
        foreach ($audios as $audio) {
            array_push($deleteUrls, [
                'id' => $audio->getId(),
                'deleteUrl' => $this->getDeleteAudioUrl($audio)
            ]);
        }

        return $deleteUrls;
    }
}

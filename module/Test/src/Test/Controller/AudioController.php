<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 11:09 AM
 */

namespace Test\Controller;

use Media\Service\File;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Media\Form\AudioUpload;
use Media\Form\Filter\AudioUploadInputFilter;

class AudioController extends AbstractActionController
{
    /**
     * @return ViewModel
     */
    public function uploadFormAction()
    {
        $fileService = new File($this->getServiceLocator());
        $this->layout('layout/dashboard/dashboard');
        return new ViewModel(['fileService' => $fileService, 'type' => \Media\Entity\File::AUDIO_FILETYPE]);
    }

    /**
     * Advanced avatar uploader Blueimp UI
     */
    public function uploadAction()
    {
        $user = $this->identity()->getUser();
        $fileService = $this->getServiceLocator()->get('Media\Service\File');
        $blueimpService = $this->getServiceLocator()->get('Media\Service\Blueimp');
        $actionName = $this->getRequest()->getUri()->getPath();

        if ($this->getRequest()->isPost()) {
            $form = new AudioUpload();
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
                $audio = $fileService->createFile($form, $this->identity()->getUser());
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->commit();
                $audios = $blueimpService->displayUploadedFile(
                    $audio,
                    $actionName
                );
            } else {
                $messages = $form->getMessages();
                $messages = array_shift($messages);
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->rollBack();
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->close();

                $audios = [ 'files' => [
                        [
                            'name' => $form->get('audio')->getValue()['name'],
                            'error' => array_shift($messages)
                        ]
                ]];
            }
        } elseif ($this->getRequest()->isDelete()) {
            $fileService
                ->deleteFile($this->getRequest()->getQuery("fileId"));
            return $blueimpService
                ->deleteFileJson($this->getRequest()->getQuery("fileId"));
        } else {
            $audios = $blueimpService->displayUploadedFiles(
                $user->getAudios(),
                $actionName
            );
        }

        return new JsonModel($audios);
    }
}

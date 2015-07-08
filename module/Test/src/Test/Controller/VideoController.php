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
use Media\Form\VideoUpload;
use Media\Form\Filter\VideoUploadInputFilter;

class VideoController extends AbstractActionController
{
    /**
     * @return ViewModel
     */
    public function uploadFormAction()
    {
        $fileService = new File($this->getServiceLocator());
        $this->layout('layout/dashboard/dashboard');
        return new ViewModel(['fileService' => $fileService, 'type' => \Media\Entity\File::VIDEO_FILETYPE]);
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
            $form = new VideoUpload();
            $inputFilter = new VideoUploadInputFilter();
            $form->setInputFilter($inputFilter->getInputFilter());

            $request = $this->getRequest();
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->beginTransaction();
            $form->setData($post);
            if ($form->isValid()) {
                $video = $fileService->createFile($form, $this->identity()->getUser());
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->commit();
                $videos = $blueimpService->displayUploadedFile(
                    $video,
                    $actionName
                );
            } else {
                if (null == $post) {
                    $messages = ['Server has not found file in Post request'];
                } else {
                    $messages = $form->getMessages();
                    $messages = array_shift($messages);
                }

                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->rollBack();
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->close();
                $videos = [ 'files' => [
                        [
                            'name' => $form->get('video')->getValue()['name'],
                            'error' => array_shift($messages)
                        ]
                ]];
            }
        } elseif($this->getRequest()->isDelete()) {
            $fileService
                ->deleteFile($this->getRequest()->getQuery("fileId"));
            return $blueimpService
                ->deleteFileJson($this->getRequest()->getQuery("fileId"));
        } else {
            $videos = $blueimpService->displayUploadedFiles(
                $user->getVideos(),
                $actionName
            );
        }

        return new JsonModel($videos);
    }
}

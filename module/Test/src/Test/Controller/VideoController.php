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
                $dataForJson = $blueimpService->displayUploadedFile($video, '/test/video/delete-video/');
            } else {
                if (null == $post) {
                    $messages = 'Server has not found file in Post request';
                } else {
                    $messages = $form->getMessages();
                    $messages = array_shift($messages);
                }

                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->rollBack();
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->close();
                $dataForJson = [ 'files' => [
                        [
                            'name' => $form->get('video')->getValue()['name'],
                            'error' => $messages
                        ]
                ]];
            }
        } else {
            $dataForJson = $blueimpService->displayUploadedFiles(
                $user->getVideos(),
                '/test/video/delete-video/'
            );
        }

        return new JsonModel($dataForJson);
    }

    /**
     * @return mixed
     */
    public function deleteVideoAction()
    {
        $this->getServiceLocator()->get('Media\Service\File')
            ->deleteFile($this->getEvent()->getRouteMatch()->getParam('id'));
        return $this->getServiceLocator()->get('Media\Service\Blueimp')
            ->deleteFileJson($this->getEvent()->getRouteMatch()->getParam('id'));
    }
}

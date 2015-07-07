<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 11:09 AM
 */

namespace Test\Controller;

use Media\Entity\File;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Media\Form\ImageUpload;
use Media\Form\Filter\ImageUploadInputFilter;

class ImageController extends AbstractActionController
{
    /**
     * @return ViewModel
     */
    public function uploadFormAction()
    {
        $fileService = $this->getServiceLocator()->get('Media\Service\File');
        $this->layout('layout/dashboard/dashboard');

        return new ViewModel([
            'fileService' => $fileService,
            'type' => File::IMAGE_FILETYPE
        ]);
    }

    /**
     * Advanced avatar uploader Blueimp UI
     */
    public function uploadAction()
    {
        $user = $this->identity()->getUser();
        $fileService = $this->getServiceLocator()
            ->get('Media\Service\File');
        $blueimpService = $this->getServiceLocator()
            ->get('Media\Service\Blueimp');
        $actionName = $this->getRequest()->getUri()->getPath();

        if ($this->getRequest()->isPost()) {
            $form = new ImageUpload();
            $inputFilter = new ImageUploadInputFilter();
            $form->setInputFilter($inputFilter->getInputFilter());

            $request = $this->getRequest();
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')
                ->getConnection()
                ->beginTransaction();

            $form->setData($post);

            if ($form->isValid()) {
                $image = $fileService->createFile(
                    $form,
                    $user
                );
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')
                    ->getConnection()
                    ->commit();
                $images = $blueimpService->displayUploadedFile(
                    $image,
                    $actionName
                );
            } else {
                $messages = $form->getMessages();
                $messages = array_shift($messages);
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')
                    ->getConnection()
                    ->rollBack();
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')
                    ->close();
                $images = [ 'files' => [
                    [
                        'name' => $form->get('image')->getValue()['name'],
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
            $images = $blueimpService->displayUploadedFiles(
                $user->getImages(),
                $actionName
            );
        }

        return new JsonModel($images);
    }
}

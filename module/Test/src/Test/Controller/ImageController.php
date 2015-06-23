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
            'imageService' => $fileService,
            'type' => File::IMAGE_FILETYPE
        ]);
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
                    $this->identity()->getUser()
                );
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')
                    ->getConnection()
                    ->commit();
                $dataForJson = $blueimpService->displayUploadedFile(
                    $image,
                    '/test/image/delete-image/'
                );
            } else {
                $messages = $form->getMessages();
                $messages = array_shift($messages);
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')
                    ->getConnection()
                    ->rollBack();
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')
                    ->close();
                $dataForJson = [ 'files' => [
                    [
                        'name' => $form->get('image')->getValue()['name'],
                        'error' => array_shift($messages)
                    ]
                ]];
            }
        } else {
            $dataForJson = $blueimpService->displayUploadedFiles(
                $user->getImages(),
                '/test/image/delete-image/'
            );
        }

        return new JsonModel($dataForJson);
    }

    /**
     * @return mixed
     */
    public function deleteImageAction()
    {
        $this->getServiceLocator()->get('Media\Service\File')
            ->deleteFile($this->getEvent()->getRouteMatch()->getParam('id'));
        return $this->getServiceLocator()->get('Media\Service\Blueimp')
            ->deleteFileJson($this->getEvent()->getRouteMatch()->getParam('id'));
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 11:09 AM
 */

namespace Test\Controller;

use Media\Service\Image;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Media\Form\ImageUpload;
use Media\Form\Filter\ImageUploadInputFilter;
use Media\Interfce\ImageUploaderInterface;

class ImageController extends AbstractActionController implements ImageUploaderInterface
{
    public function uploadImageAction()
    {
        $form = new ImageUpload('upload-image');
        $imageService = new Image($this->getServiceLocator());
        return new ViewModel(['form' => $form, 'imageService' => $imageService]);
    }

    /**
     * Advanced avatar uploader Blueimp UI
     */
    public function startUploadAction()
    {

        $user = $this->identity()->getUser();
        $imageService = $this->getServiceLocator()->get('Media\Service\Image');
        $blueimpService = $this->getServiceLocator()->get('Media\Service\Blueimp');
        if ($this->getRequest()->isPost()) {
            $form = new ImageUpload('upload-image');
            $inputFilter = new ImageUploadInputFilter();
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
                $image = $imageService->createImage($data, $this->identity()->getUser());
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->commit();
                $dataForJson = $blueimpService->displayUploadedFile($image, $this->getDeleteUrl($image));
            } else {
                $messages = $form->getMessages();
                $messages = array_shift($messages);
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->rollBack();
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->close();

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
                $this->getDeleteUrls($user->getImages())
            );
        }

        return new JsonModel($dataForJson);
    }

    public function deleteImageAction()
    {
        $this->getServiceLocator()->get('Media\Service\Image')
            ->deleteImage($this->getEvent()->getRouteMatch()->getParam('id'));
        return $this->getServiceLocator()->get('Media\Service\Blueimp')
            ->deleteFileJson($this->getEvent()->getRouteMatch()->getParam('id'));
    }

    public function getDeleteUrl($image)
    {
        $url = $this->serviceLocator->get('ViewHelperManager')->get('url');
        $imageService = $this->getServiceLocator()->get('Media\Service\Image');
        return $imageService->getFullUrl($url('test/default', [
            'controller' => 'image',
            'action' => 'delete-image',
            'id' => $image->getId()
        ]));
    }

    public function getDeleteUrls($images)
    {
        $deleteUrls = [];
        foreach ($images as $image) {
            array_push($deleteUrls, [
                'id' => $image->getId(),
                'deleteUrl' => $this->getDeleteUrl($image)
            ]);
        }

        return $deleteUrls;
    }
}

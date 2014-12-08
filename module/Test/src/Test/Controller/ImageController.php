<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 11:09 AM
 */

namespace Test\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Media\Form\ImageUpload;
use Media\Form\Filter\ImageUploadInputFilter;

class ImageController extends AbstractActionController
{
    public function indexAction()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $imageService = $this->getServiceLocator()->get('Media\Service\Image');

        $userEntityRepository = $entityManager->getRepository('User\Entity\User');
        $user = $userEntityRepository->findOneById($this->identity()->getUser()->getId());
        $imagesId = $user->getImages();
        $imagesLocation = [];
        $imagesUrl = [];
        $thumbsUrl = [];
        foreach ($imagesId as $imageId) {
            $image = $entityManager->getRepository('Media\Entity\Image')->findOneById($imageId);
            array_push($imagesLocation, $image->getLocation());
            array_push($imagesUrl, $imageService->getFullUrl($image->getUrlPart()));
            array_push($thumbsUrl, $imageService->getFullUrl($image->getThumb()));
        }

        return new ViewModel(
            [
                'imagesId' => $imagesId,
                'imagesLocation' => $imagesLocation,
                'imagesUrl' => $imagesUrl,
                'thumbsUrl' => $thumbsUrl
            ]
        );
    }

    /**
     * Advanced avatar uploader Blueimp UI
     */
    public function uploadImageAction()
    {
        if ($this->getRequest()->isPost()) {
            $user = $this->identity()->getUser();

            $imageService = $this->getServiceLocator()->get('Media\Service\Image');
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

            //At this moment filter is used
            if ($form->isValid()) {
                $data = $form->getData();
                $image = $imageService->createImage($data, $this->identity()->getUser());
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->commit();

                return new ViewModel(['image' => $image]);
            } else {
                $messages = $form->getMessages();
                $messages = array_shift($messages);
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->rollBack();
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->close();

                return new ViewModel(
                    [
                        'message' => [
                            'name' => $form->get('image')->getValue()['name'],
                            'error' => array_shift($messages)
                        ]
                    ]
                );
            }
        }
        $form = new ImageUpload('upload-image');

        return new ViewModel(array('form' => $form));
    }
}

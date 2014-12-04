<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 11:09 AM
 */

namespace Media\Controller;

use Media\Entity\ObjectImage;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Media\Form\ImageUpload;
use Media\Form\Filter\ImageUploadInputFilter;

class ImageController extends AbstractActionController
{
    public function indexAction()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $userEntityRepository = $entityManager->getRepository('User\Entity\User');
        $user = $userEntityRepository->findOneById(11);
        $imagesId = $user->getImages();
        $imagesUrl = [];
        foreach ($imagesId as $imageId) {
            $image = $entityManager->getRepository('Media\Entity\Image')->findOneById($imageId);
            array_push($imagesUrl, $image->getDestination());
        }

        return new ViewModel(['imagesId' => $imagesId, 'imagesUrl' => $imagesUrl]);
    }

    /**
     * Advanced avatar uploader Blueimp UI
     */
    public function uploadImageAction()
    {
        if ($this->getRequest()->isPost()) {
            $user = $this->identity()->getUser();

            $imageService = new \Media\Service\Image($this->getServiceLocator());
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

            if ($form->isValid()) { //At this moment filter is used
                $data = $form->getData();
                $image = $imageService->createImage($data, $this->identity());
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->commit();

                $objectImage = new ObjectImage();
                $objectImage->setImage($image);
                $objectImage->setEntityName($user->getEntityName());
                $objectImage->setObjectId($user->getId());
                $this->getServiceLocator()->get('doctrine.entitymanager.orm_default')->persist($objectImage);
                $this->getServiceLocator()->get('doctrine.entitymanager.orm_default')->flush();

                return new ViewModel(['image' => $image]);
            } else {
                $messages = $form->getMessages();
                $messages = array_shift($messages);
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->getConnection()->rollBack();
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->close();

                return new ViewModel([
                    'message' => [
                        'name' => $form->get('image')->getValue()['name'],
                        'error' => array_shift($messages)
                    ]
                ]);
            }
        }
        $form = new ImageUpload('upload-image');

        return new ViewModel(array('form' => $form));
    }
}
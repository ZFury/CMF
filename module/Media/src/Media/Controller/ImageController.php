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

class ImageController extends AbstractActionController
{
    public function indexAction()
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $userEntityRepository = $entityManager->getRepository('User\Entity\User');
        $user = $userEntityRepository->findOneById(11);
        var_dump($user->getImages());die();
    }
}
<?php

namespace Comment\Service;

use Zend\ServiceManager\ServiceManager;
use Comment\Form;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class Comment
{
    /**
     * @var null|\Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager = null;

    /**
     * @return null|ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceManager;
    }

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->serviceManager = $sm;
    }

    /**
     * @param Form\Add $form
     * @param $data
     * @param $entityType
     * @param $entityId
     * @param $user
     * @throws \Exception
     */
    public function addComment(Form\Add $form, $data, $entityType, $entityId, $user)
    {

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $form->setData($data);
        if ($form->isValid()) {
            $data = $form->getData();
            $comment = new \Comment\Entity\Comment();

            $objectManager->getConnection()->beginTransaction();
            try {
                $hydrator = new DoctrineHydrator($objectManager);
                $hydrator->hydrate($data, $comment);
                $comment->setUser($user);
                $comment->setEntityType($entityType->getEntityType());
                $comment->setEntityId($entityId);
                $objectManager->persist($comment);
                $objectManager->flush();
                $objectManager->getConnection()->commit();

                return $comment;
            } catch (\Exception $e) {
                $objectManager->getConnection()->rollback();
                throw $e;
            }
        }
    }

    /**
     * @param $entityType
     * @param $entityId
     * @param null $userId
     * @return array
     */
    public function getCommentsByEntityId($entityType, $entityId, $userId = null)
    {
        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');

        if (isset($userId)) {
            $comments = $objectManager->getRepository('Comment\Entity\Comment')->findBy(array('entityType' => $entityType, 'entityId' => $entityId, 'userId' => $userId));
        } else {
            $comments = $objectManager->getRepository('Comment\Entity\Comment')->findBy(array('entityType' => $entityType, 'entityId' => $entityId));
        }

        $arrayComments = array();
        foreach ($comments as $comment) {
            $arrayComments[$comment->getId()]['comment'] = $comment;
            $arrayComments[$comment->getId()]['childs'] = self::getCommentsByEntityId('comment', $comment->getId());
        }
        return $arrayComments;
    }

    /**
     * @param $userId
     * @return array
     */
    /*public function getCommentsByUserId($userId)
    {
        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');

        $masObj = $objectManager->getRepository('Comment\Entity\Comment')->findBy(array('userId' => $userId));

        $masResult = array();
        foreach ($masObj as $obj) {
            if (strcmp($obj->getEntityType(), 'comment')) {
                $masResult[$obj->getId()]['comment_info'] = $obj;
                $masResult[$obj->getId()]['comments'] = self::getCommentsByEntityId('comment', $obj->getId(), $userId);
            }
        }
        return $masResult;
    }*/

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function deleteCommentById($id)
    {
        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
        $objectManager->getConnection()->beginTransaction();
        $comment = $objectManager->find('Comment\Entity\Comment', $id);

        if (!$comment) {
            throw new \Exception("Attempt to remove comments that do not exist");
        }
        try {
            $objectManager->remove($comment);
            $objectManager->flush();
            $objectManager->getConnection()->commit();
        } catch (\Exception $e) {
            $objectManager->getConnection()->rollback();
            throw $e;
        }

        return true;
    }

    /**
    * @param $form
    * @param $id
     */
    public function editCommentById($form, $id)
    {

    }
}

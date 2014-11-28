<?php

namespace Comment\Service;

use Zend\ServiceManager\ServiceManager;
use Doctrine\ORM\Query\ResultSetMapping;

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
     * @param $entityType
     * @param $entityId
     * @param null $userId
     * @return array
     */
    public function getCommentsByEntityId($entityType, $entityId, $userId = null)
    {
        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');

        if (isset($userId)) {
            $masObj = $objectManager->getRepository('Comment\Entity\Comment')->findBy(array('entityType' => $entityType, 'entityId' => $entityId, 'userId' => $userId));
        } else {
            $masObj = $objectManager->getRepository('Comment\Entity\Comment')->findBy(array('entityType' => $entityType, 'entityId' => $entityId));
        }


        $masResult = array();
        foreach ($masObj as $obj) {
            $masResult[$obj->getId()]['comment_info'] = $obj;
            $masResult[$obj->getId()]['comments'] = self::getCommentsByEntityId('comment', $obj->getId());
        }
        return $masResult;
    }

    /**
     * @param $userId
     * @return array
     */
    public function getCommentsByUserId($userId)
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
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function deleteCommentById($id)
    {
        if (!isset($id)) {
            return false;
        } else {
            $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');
            try {
                $objectManager->getConnection()->beginTransaction();
                $comment = $objectManager->find('Comment\Entity\Comment', $id);
                $objectManager->remove($comment);
                $objectManager->flush();
                $objectManager->getConnection()->commit();
                return true;
            } catch (\Exception $e) {
                $objectManager->getConnection()->rollback();
                throw $e;
            }
        }
    }
}

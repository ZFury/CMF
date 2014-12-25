<?php

namespace Comment\Service;

use Zend\ServiceManager\ServiceManager;

class EntityType
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
     * @param $aliasEntity
     * @param $entityId
     * @return \Comment\Entity\EntityType
     * @throws \Exception
     */
    public function checkEntity($aliasEntity, $entityId)
    {
        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');

        if (!$entityType = $objectManager->getRepository('Comment\Entity\EntityType')->getEntityType($aliasEntity)) {
            throw new \Exception('Unknown entity type');
        }

        $entity = $objectManager->getRepository($entityType->getEntity())->find($entityId);
        if (!count($entity)) {
            throw new \Exception('Unknown entity');
        }

        return $entityType;
    }
}

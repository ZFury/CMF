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
     * @return bool
     */
    public function get($aliasEntity, $entityId)
    {
        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');

        $match = $objectManager->getRepository('Comment\Entity\EntityType')->findOneBy(array('aliasEntity' => $aliasEntity));
        if(count($match)==0) {
            return false;
        }
        $entity = $objectManager->getRepository($match->getEntity())->find($entityId);
        if(count($entity)==0) {
            return false;
        }
        return true;
    }
}

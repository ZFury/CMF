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
     * @param $entityType
     * @return mixed
     */
    public function get($entityType)
    {
        $objectManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');

        return $objectManager->getRepository('Comment\Entity\EntityType')->findOneBy(array('entityType' => $entityType));
    }
}

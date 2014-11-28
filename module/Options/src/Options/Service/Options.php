<?php

namespace Options\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Controller\Plugin\Url;

/**
 * Class Options
 * @package Options\Service
 */
class Options
{

    /**
     *  default namespace
     */
    const NAMESPACE_DEFAULT = 'default';

    /**
     * @var null|\Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager = null;

    /**
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->serviceManager = $sm;
    }

    /**
     * @return null|ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceManager;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getObjectManager()
    {
        return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    }

    /**
     * get option by key & namespace
     *
     * @param $key
     * @param string $namespace
     * @return mixed
     */
    public function getOption($key, $namespace = self::NAMESPACE_DEFAULT)
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $option = $objectManager
            ->getRepository('Options\Entity\Options')
            ->find(array('namespace' => $namespace, 'key' => $key));
        return $option;
    }

    /**
     * get options by namespace
     *
     * @param string $namespace
     * @return mixed
     */
    public function getNamespace($namespace = self::NAMESPACE_DEFAULT)
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $option = $objectManager
            ->getRepository('Options\Entity\Options')
            ->findBy(array('namespace' => $namespace));
        return $option;
    }

    /**
     * @param $key
     * @param $value
     * @param string $namespace
     * @param null $description
     */
    public function setOption($key, $value, $namespace = 'default', $description = null)
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        /** @var \Options\Entity\Options $option */
        $option = $this->getServiceLocator()->get('Options\Entity\Options');

        $objectManager->getConnection()->beginTransaction();



        $option->setNamespace($namespace);
        $option->setKey($key);
        $option->setValue($value);
        $option->setDescription($description);
        $option->setCreated(new \DateTime(date('Y-m-d H:i:s')));
        $option->setUpdated(new \DateTime(date('Y-m-d H:i:s')));

        $objectManager->persist($option);
        $objectManager->flush();

        $objectManager->getConnection()->commit();
    }
}

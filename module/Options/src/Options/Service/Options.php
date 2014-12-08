<?php

namespace Options\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Controller\Plugin\Url;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Class Options
 * @package Options\Service
 */
class Options
{
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
     * @param  $key
     * @param  string $namespace
     * @return mixed
     */
    public function getOption($key, $namespace = \Options\Entity\Options::NAMESPACE_DEFAULT)
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
     * @param  string $namespace
     * @return mixed
     */
    public function getNamespace($namespace = \Options\Entity\Options::NAMESPACE_DEFAULT)
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $option = $objectManager
            ->getRepository('Options\Entity\Options')
            ->findBy(array('namespace' => $namespace));
        return $option;
    }

    /**
     * set option
     *
     * @param  $key
     * @param  $value
     * @param  string $namespace
     * @param  null   $description
     * @throws \Exception
     */
    public function setOption($key, $value, $namespace = \Options\Entity\Options::NAMESPACE_DEFAULT, $description = null)
    {
        $data = array(
            'key' => $key,
            'namespace' => $namespace,
            'value' => $value,
            'description' => $description
        );

        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        /**
        * @var \Options\Entity\Options $option */
        $option = $this->getServiceLocator()->get('Options\Entity\Options');
        $objectManager->getConnection()->beginTransaction();

        try {
            $hydrator = new DoctrineHydrator($objectManager);

            $hydrator->hydrate($data, $option);

            $option->setCreated(new \DateTime(date('Y-m-d H:i:s')));
            $option->setUpdated(new \DateTime(date('Y-m-d H:i:s')));

            $objectManager->persist($option);
            $objectManager->flush();

            $objectManager->getConnection()->commit();
        } catch (\Exception $e) {
            $objectManager->getConnection()->rollback();
            throw $e;
        }
    }
}

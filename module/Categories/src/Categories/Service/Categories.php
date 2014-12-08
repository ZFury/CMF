<?php

namespace Categories\Service;

use Zend\ServiceManager\ServiceManager;

/**
 * Class Categories
 * @package Categories\Service
 */
class Categories
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
     * Updates a `path` field of each child of parent category.
     *
     * @param \Categories\Entity\Categories $category Parent category
     */
    public function updateChildrenPath(\Categories\Entity\Categories $category)
    {
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $children = $category->getChildren();
        $this->recursiveUpdatePath($children->toArray(), $category->getPath(), $entityManager);
        $entityManager->flush();
    }

    /**
     * Recursively search category tree and sets path for each node.
     *
     * @param  $tree array Category tree
     * @param  $path string Path of each parent
     * @param  $entityManager \Doctrine\ORM\EntityManager
     * @return bool
     */
    private function recursiveUpdatePath($tree, $path, $entityManager)
    {
        foreach ($tree as $node) {
            $node->setPath($path . '/' . $node->getAlias());
            $entityManager->persist($node);
            if (count($node->getChildren()) != 0) {
                $this->recursiveUpdatePath($node->getChildren()->toArray(), $node->getPath(), $entityManager);
            }
        }
        return true;
    }

    /**
     * Gets tree for some root category.
     *
     * @param  $alias Alias of the root category
     * @return null|\Categories\Entity\Categories
     */
    public function getTreeForRoot($alias)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $entityManager
         */
        $entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        return $entityManager->getRepository('Categories\Entity\Categories')->findOneBy(['parentId' => null, 'alias' => $alias]);
    }
}

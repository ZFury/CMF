<?php

namespace Comment\Repository;

use Doctrine\ORM\EntityRepository;

class EntityType extends EntityRepository
{
    /**
     * @return array
     */
    public function getEntities()
    {
        $entityManager = $this->getEntityManager();
        $entities = array();
        $meta = $entityManager->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $m) {
            if ($m->getName() === "Comment\\Entity\\EntityType") {
                continue;
            }
            $entities[$m->getName()] = $m->getName();
        }

        return $entities;
    }

    /**
     * @param $aliasEntity
     * @return null|object
     */
    public function getEntityType($aliasEntity)
    {
        $entity = $this->findOneBy(['aliasEntity' => $aliasEntity]);
        return $entity;
    }

    /**
     * @param $entity
     * @return null|object
     */
    public function getEntityTypeByEntity($entity)
    {
        $entities = $this->findOneBy(['entity' => $entity]);
        return $entities;
    }
}

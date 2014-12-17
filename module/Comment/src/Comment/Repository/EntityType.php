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
            $entities[$m->getName()] = $m->getName();
        }
        return $entities;
    }

    /**
     * @param $aliasEntity
     * @return mixed
     */
    public function getEntityType($aliasEntity)
    {
        $comments = $this->findOneBy(['aliasEntity' => $aliasEntity]);
        return $comments;
    }
}

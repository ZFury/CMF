<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 4:52 PM
 */

namespace Starter\Media;

use Doctrine\ORM\Event\LifecycleEventArgs;

trait Audio
{
    public abstract function getEntityName();

    public abstract function setLifecycleArgs(LifecycleEventArgs $args);

    /**
     * Returns an array of ids
     *
     * @return mixed
     */
    public function getAudios()
    {
        $qb = $this->lifecycleArgs->getEntityManager()->createQueryBuilder();
        $subQb = $this->lifecycleArgs->getEntityManager()->createQueryBuilder();
        $subQb->select('oi.fileId')
            ->from('Media\Entity\ObjectFile', 'oi')
            ->where('oi.entityName=:name')
            ->andWhere('oi.objectId=:id')
            ->setParameter('name', $this->getEntityName())
            ->setParameter('id', $this->id);
        $results = $subQb->getQuery()->getResult();

        if (!$results) {
            return [];
        }

        foreach ($results as $result) {
            array_push($results, $result['fileId']);
            array_shift($results);
        }

        $qb->select('i')
            ->from('Media\Entity\File', 'i')
            ->where($qb->expr()->in('i.id', $results));

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 4:52 PM
 */

namespace Starter\Media;

use Doctrine\ORM\Event\LifecycleEventArgs;

trait Image
{
    public abstract function getEntityName();

    public abstract function setLifecycleArgs(LifecycleEventArgs $args);

    /**
     * Returns an array of ids
     *
     * @return mixed
     */
    public function getImages()
    {
        $q = $this->lifecycleArgs->getEntityManager()->createQueryBuilder()
            ->select('oi.imageId')
            ->from('Media\Entity\ObjectImage', 'oi')
            ->where('oi.entityName=:name')
            ->andWhere('oi.objectId=:id')
            ->setParameter('name', $this->getEntityName())
            ->setParameter('id', $this->id)
            ->getQuery();

        $results = $q->getResult();

        foreach ($results as $result) {
            array_push($results, $result['imageId']);
            array_shift($results);
        }

        return $results;
    }
}

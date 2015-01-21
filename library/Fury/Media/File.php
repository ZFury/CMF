<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 4:52 PM
 */

namespace Fury\Media;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Query\Expr\Join;

trait File
{
    public abstract function getEntityName();

    public abstract function setEntityManager(LifecycleEventArgs $args);

    public abstract function getId();

    /**
     * Returns an array of ids
     *
     * @return mixed
     */
    public function getImages()
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('f')
            ->from('\Media\Entity\File', 'f')
            ->innerJoin('\Media\Entity\ObjectFile', 'obf', Join::WITH, 'f.id = obf.fileId')
            ->where('f.type = :type')
            ->andWhere('obf.entityName = :name')
            ->andWhere('obf.objectId = :id')
            ->setParameter('type', \Media\Entity\File::IMAGE_FILETYPE)
            ->setParameter('name', $this->getEntityName())
            ->setParameter('id', $this->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * Returns an array of ids
     *
     * @return mixed
     */
    public function getAudios()
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('f')
            ->from('\Media\Entity\File', 'f')
            ->innerJoin('\Media\Entity\ObjectFile', 'obf', Join::WITH, 'f.id = obf.fileId')
            ->where('f.type = :type')
            ->andWhere('obf.entityName = :name')
            ->andWhere('obf.objectId = :id')
            ->setParameter('type', \Media\Entity\File::AUDIO_FILETYPE)
            ->setParameter('name', $this->getEntityName())
            ->setParameter('id', $this->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * Returns an array of ids
     *
     * @return mixed
     */
    public function getVideos()
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('f')
            ->from('\Media\Entity\File', 'f')
            ->innerJoin('\Media\Entity\ObjectFile', 'obf', Join::WITH, 'f.id = obf.fileId')
            ->where('f.type = :type')
            ->andWhere('obf.entityName = :name')
            ->andWhere('obf.objectId = :id')
            ->setParameter('type', \Media\Entity\File::VIDEO_FILETYPE)
            ->setParameter('name', $this->getEntityName())
            ->setParameter('id', $this->getId());

        return $qb->getQuery()->getResult();
    }
}

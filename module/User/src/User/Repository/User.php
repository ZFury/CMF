<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 29.08.14
 * Time: 15:31
 */

namespace User\Repository;

use Doctrine\ORM\EntityRepository;

class User extends EntityRepository
{
    /**
     * Return count searched users
     *
     * @return int
     *
     * Created by Maxim Mandryka maxim.mandryka@nixsolutions.com
     */
    public function countUsers()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $select = $qb->select('count(u.id)')
            ->from('\User\Entity\User', 'u');

        $count = $select->getQuery()->getSingleScalarResult();

        return $count;
    }
}
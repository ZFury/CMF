<?php

namespace Test\Repository;

use Doctrine\ORM\EntityRepository;

class Test extends EntityRepository
{
    /**
     * Return count search users
     *
     * @return int
     *
     * Created by Maxim Mandryka maxim.mandryka@nixsolutions.com
     */
    public function countSearchTests($searchString)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $select = $qb->select('count(test.id)')
            ->from('\Test\Entity\Test', 'test');
        if (!empty($searchString)) {
            $select->where(
                $qb->expr()->orX()
                    ->add($qb->expr()->like('test.email', $qb->expr()->literal('%' . $searchString . '%')))
            );
        }

        $count = $select->getQuery()->getSingleScalarResult();

        return $count;
    }
}

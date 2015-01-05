<?php

namespace Comment\Grid\Comment;

use Starter\Grid\AbstractGrid;

class Grid extends AbstractGrid
{
    public function init()
    {
        $em = $this->sm->get('Doctrine\ORM\EntityManager');
        $source = $em->createQueryBuilder()->select([
            'comment.id',
            'comment.comment',
            'user.displayName',
            'entity_type.aliasEntity'
        ])
            ->from('\Comment\Entity\Comment', 'comment')
            ->innerJoin('comment.user', 'user')
            ->innerJoin('comment.entityType', 'entity_type');
        $this->setSource($source)->setEntityAlias('comment')
            ->setColumns([
                'Id' => 'id',
                'Comment' => 'comment',
                'User(author)' => 'displayName',
                'Entity' => 'aliasEntity'])
            ->setAllowedFilters(['comment', 'displayName', 'aliasEntity'])
            ->setAllowedOrders(['aliasEntity', 'displayName']);
    }
}

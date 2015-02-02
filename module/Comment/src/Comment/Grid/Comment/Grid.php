<?php

namespace Comment\Grid\Comment;

use Fury\Grid\AbstractGrid;

class Grid extends AbstractGrid
{
    /**
     * Grid
     */
    public function init()
    {
        $em = $this->sm->get('Doctrine\ORM\EntityManager');
        $source = $em->createQueryBuilder()->select([
            'comment.id',
            'comment.comment',
            'user.displayName',
            'entity_type.alias'
        ])
            ->from('\Comment\Entity\Comment', 'comment')
            ->innerJoin('comment.user', 'user')
            ->innerJoin('comment.entityType', 'entity_type');
        $this->setSource($source)->setEntityAlias('comment')
            ->setColumns([
                'comment.id' => 'Id',
                'comment.comment' => 'comment',
                'user.displayName' => 'User(author)',
                'entity_type.alias' => 'Entity',
            ])
            ->setAllowedFilters(['comment.comment', 'user.displayName', 'entity_type.alias'])
            ->setAllowedOrders(['entity_type.alias', 'user.displayName']);
    }
}

<?php

namespace Comment\Grid\EntityType;

use Starter\Grid\AbstractGrid;

class Grid extends AbstractGrid
{
    public function init()
    {
        $em = $this->sm->get('Doctrine\ORM\EntityManager');
        $source = $em->createQueryBuilder()->select([
            'entity_type.id',
            'entity_type.aliasEntity',
            'entity_type.entity',
            'entity_type.description',
            'entity_type.visibleComment',
            'entity_type.enabledComment'
        ])
            ->from('\Comment\Entity\EntityType', 'entity_type');
        $this->setSource($source)->setEntityAlias('entity_type')
            ->setColumns([
                'Id' => 'id',
                'Alias' => 'aliasEntity',
                'Entity' => 'entity',
                'Description' => 'description',
                'Visible comments' => 'visibleComment',
                'Possible to comment' => 'enabledComment'])
            ->setAllowedFilters(['aliasEntity', 'entity'])->setAllowedOrders(['aliasEntity']);
    }
}

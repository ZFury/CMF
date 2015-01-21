<?php

namespace Comment\Grid\EntityType;

use Fury\Grid\AbstractGrid;

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
                'entity_type.id' => 'Id',
                'entity_type.aliasEntity' => 'Alias',
                'entity_type.entity' => 'Entity',
                'entity_type.description' => 'Description',
                'entity_type.visibleComment' => 'Visible comments',
                'entity_type.enabledComment' => 'Possible to comment',
            ])
            ->setAllowedFilters(['entity_type.aliasEntity', 'entity_type.entity'])
            ->setAllowedOrders(['entity_type.aliasEntity']);
    }
}

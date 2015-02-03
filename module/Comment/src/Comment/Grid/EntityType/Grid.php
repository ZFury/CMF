<?php

namespace Comment\Grid\EntityType;

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
            'entity_type.id',
            'entity_type.alias',
            'entity_type.entity',
            'entity_type.description',
            'entity_type.isVisible',
            'entity_type.isEnabled'
        ])
            ->from('\Comment\Entity\EntityType', 'entity_type');
        $this->setSource($source)->setEntityAlias('entity_type')
            ->setColumns([
                'entity_type.id' => 'Id',
                'entity_type.alias' => 'Alias',
                'entity_type.entity' => 'Entity',
                'entity_type.description' => 'Description',
                'entity_type.isVisible' => 'Visible comments',
                'entity_type.isEnabled' => 'Possible to comment',
            ])
            ->setAllowedFilters(['entity_type.alias', 'entity_type.entity'])
            ->setAllowedOrders(['entity_type.alias']);
    }
}

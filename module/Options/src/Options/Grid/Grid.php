<?php

namespace Options\Grid;

use Starter\Grid\AbstractGrid;

class Grid extends AbstractGrid
{
    public function init()
    {
        $em = $this->sm->get('Doctrine\ORM\EntityManager');
        $source = $em->createQueryBuilder()
            ->select(['options.namespace', 'options.key', 'options.value', 'options.description',
                'options.created', 'options.updated'])
            ->from('\Options\Entity\Options', 'options');
        $this->setSource($source)->setColumns(
            [
                'options.namespace' => 'Namespace',
                'options.key' => 'Key',
                'options.value' => 'Value',
                'options.description' => 'Description',
                'options.created' => 'Created',
                'options.updated' => 'Updated',
            ]
        )->setAllowedFilters(['options.namespace', 'options.key', 'options.description'])
            ->setAllowedOrders(
                [
                    'options.namespace',
                    'options.key',
                    'options.value',
                    'options.description',
                    'options.created',
                    'options.updated'
                ]
            );
    }
}

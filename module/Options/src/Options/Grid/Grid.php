<?php

namespace Options\Grid;

use Starter\Mvc\Grid\AbstractGrid;

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
                'Namespace' => 'namespace',
                'Key' => 'key',
                'Value' => 'value',
                'Description' => 'description',
                'Created' => 'created',
                'Updated' => 'updated'
            ]
        )->setAllowedFilters(['namespace', 'key', 'description'])
            ->setAllowedOrders(['namespace', 'key', 'value', 'description', 'created', 'updated']);
    }
}
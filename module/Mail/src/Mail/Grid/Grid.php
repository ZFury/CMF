<?php

namespace Mail\Grid;

use Starter\Grid\AbstractGrid;

class Grid extends AbstractGrid
{
    public function init()
    {
        $em = $this->sm->get('Doctrine\ORM\EntityManager');
        $source = $em->createQueryBuilder()
            ->select(['mail.id', 'mail.alias', 'mail.description', 'mail.created', 'mail.updated'])
            ->from('\Mail\Entity\Mail', 'mail');
        $this->setSource($source)->setColumns(
            [
                'mail.id' => 'Id',
                'mail.alias' => 'Alias',
                'mail.description' => 'Description',
                'mail.created' => 'Created',
                'mail.updated' => 'updated',
            ]
        )->setAllowedFilters(['mail.alias', 'mail.description'])
            ->setAllowedOrders(['mail.id', 'mail.alias', 'mail.description', 'mail.created', 'mail.updated'])
            ->setSphinxIndex('mailIndex');
    }
}

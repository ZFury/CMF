<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 4:28 PM
 */

namespace Media\Repository;

use Doctrine\ORM\EntityRepository;

class ObjectFile extends EntityRepository
{
    /**
     * getObjectFileRow
     *
     * @param  string $provider
     * @param  string $foreignKey
     * @return AbstractRow
     */
    public function getFileRow($provider, $foreignKey)
    {
        return $this->findOneBy(['provider' => $provider, 'foreignKey' => $foreignKey]);
    }
}

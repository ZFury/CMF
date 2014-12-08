<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 4:28 PM
 */

namespace Media\Repository;

use Doctrine\ORM\EntityRepository;

class ObjectImage extends EntityRepository
{
    /**
     * getObjectImageRow
     *
     * @param  string $provider
     * @param  string $foreignKey
     * @return AbstractRow
     */
    public function getImageRow($provider, $foreignKey)
    {
        return $this->findOneBy(['provider' => $provider, 'foreignKey' => $foreignKey]);
    }
}

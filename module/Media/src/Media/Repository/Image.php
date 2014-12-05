<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 12:18 PM
 */

namespace Media\Repository;

use Doctrine\ORM\EntityRepository;

class Image extends EntityRepository
{
    /**
     * getImageRow
     *
     * @param string $provider
     * @param string $foreignKey
     * @return AbstractRow
     */
    public function getImageRow($provider, $foreignKey)
    {
        return $this->findOneBy(['provider' => $provider, 'foreignKey' => $foreignKey]);
    }
}

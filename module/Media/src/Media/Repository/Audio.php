<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 12/3/14
 * Time: 12:18 PM
 */

namespace Media\Repository;

use Doctrine\ORM\EntityRepository;

class Audio extends EntityRepository
{
    /**
     * getAudioRow
     *
     * @param  string $provider
     * @param  string $foreignKey
     * @return AbstractRow
     */
    public function getAudioRow($provider, $foreignKey)
    {
        return $this->findOneBy(['provider' => $provider, 'foreignKey' => $foreignKey]);
    }
}

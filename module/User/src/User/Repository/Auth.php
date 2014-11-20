<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 29.08.14
 * Time: 15:31
 */

namespace User\Repository;

use Doctrine\ORM\EntityRepository;

class Auth extends EntityRepository
{
    /**
     * getAuthRow
     *
     * @param string $provider
     * @param string $foreignKey
     * @return AbstractRow
     */
    public function getAuthRow($provider, $foreignKey)
    {
        return $this->findOneBy(['provider' => $provider, 'foreignKey' => $foreignKey]);
    }
} 
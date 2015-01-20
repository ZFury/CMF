<?php

namespace Mail\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class Mail
 * @package Mail\Repository
 */
class Mail extends EntityRepository
{
    /**
     * @param $alias
     * @return null|object
     */
    public function getTemplate($alias)
    {
        return $this->findOneBy(['alias' => $alias]);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: alexfloppy
 */

namespace Fury\DBAL\Entity;

/**
 * Class EntityBase
 * @package Fury\DBAL\Entity
 */
abstract class EntityBase
{
    /**
     * @return mixed
     */
    abstract public function toArray();
}

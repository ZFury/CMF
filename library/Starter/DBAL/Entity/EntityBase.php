<?php
/**
 * Created by PhpStorm.
 * User: alexfloppy
 */

namespace Starter\DBAL\Entity;

/**
 * Class EntityBase
 * @package Starter\DBAL\Entity
 */
abstract class EntityBase
{
    /**
     * @return mixed
     */
    abstract  function toArray();
}

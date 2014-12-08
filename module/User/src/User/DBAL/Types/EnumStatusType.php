<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 02.12.14
 * Time: 18:01
 */
namespace User\DBAL\Types;

use Starter\DBAL\Types\EnumType;

/**
 * Class EnumStatusType
 * Provide ability to use enum field for users table
 *
 * @package User\DBAL\Types
 */
class EnumStatusType extends EnumType
{
    /**
     * @var string $name
     */
    protected $name = 'enumstatus';

    /**
     * @var array $values
     */
    protected $values = array('active', 'inactive', 'unconfirmed');
}

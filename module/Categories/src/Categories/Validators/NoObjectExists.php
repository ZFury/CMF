<?php
/**
 * Created by PhpStorm.
 * User: babich
 * Date: 21.11.14
 * Time: 12:27
 */

namespace Categories\Validators;

use Zend\Validator\Exception;

class NoObjectExists
{
    private $objectRepository;

    public function __construct($objectRepository)
    {
        $this->objectRepository = $objectRepository;
    }

    /**
     * Checkes if value is valid.
     *
     * @param $value
     * @param $id
     * @return bool
     */
    public function isValid($value, $id)
    {
        $match = $this->objectRepository->findBy($value);

        if ((count($match) == 1 && $match[0]->getId() == $id) || empty($match)) {
            return true;
        }

        return false;
    }
}

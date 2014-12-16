<?php
/**
 * Created by PhpStorm.
 * User: Lopay
 * Date: 16.12.14
 * Time: 10:31
 */

namespace Comment\Validators;

use Zend\Validator\Exception;

class NoObjectExists
{
    private $objectRepository;

    public function __construct($objectRepository)
    {
        $this->objectRepository = $objectRepository;
    }

    /**
     * @param $data
     * @param $id
     * @return bool
     */
    public function isValid($data, $id)
    {
        $match = $this->objectRepository->findBy($data);
        if ((count($match) == 1 && $match[0]->getId() == $id) || empty($match)) {
            return true;
        }

        return false;
    }
}

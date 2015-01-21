<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link    https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User\Provider\Identity;

use BjyAuthorize\Provider\Identity;
use BjyAuthorize\Exception\InvalidRoleException;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\ServiceManager;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Identity provider based on {@see \Zend\Db\Adapter\Adapter}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class DoctrineProvider implements \BjyAuthorize\Provider\Identity\ProviderInterface
{
    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $defaultRole;

    /**
     * @param ServiceManager $sm
     */
    public function setServiceLocator(ServiceManager $sm)
    {
        $this->serviceManager = $sm;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceManager;
    }

    /**
     * @param EntityManager $entityManager
     * @param AuthenticationService $authService
     */
    public function __construct(EntityManager $entityManager, AuthenticationService $authService)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles()
    {
        //if user was manually deleted from storage we should clear identity
        if ($this->authService->hasIdentity() && !$this->authService->getIdentity()) {
            $this->authService->clearIdentity();
        }
        if (!$this->authService->hasIdentity()) {
            return array($this->getDefaultRole());
        }

        return $this->authService->getIdentity()->getUser()->getRole();
    }

    /**
     * @return string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    public function getDefaultRole()
    {
        return $this->defaultRole;
    }

    /**
     * @param string|\Zend\Permissions\Acl\Role\RoleInterface $defaultRole
     *
     * @throws \BjyAuthorize\Exception\InvalidRoleException
     */
    public function setDefaultRole($defaultRole)
    {
        if (!($defaultRole instanceof RoleInterface || is_string($defaultRole))) {
            throw InvalidRoleException::invalidRoleInstance($defaultRole);
        }

        $this->defaultRole = $defaultRole;
    }
}

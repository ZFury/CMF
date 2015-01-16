<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Cron\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Class SphinxController
 * @package Cron\Controller
 */
class SphinxController extends AbstractActionController
{
    /**
     * @return array|ViewModel
     * @throws EntityNotFoundException
     */
    public function indexAction()
    {
        return exec('indexer --rotate --all');
    }

    /**
     * @return string
     */
    public function rotateAllIndexesAction()
    {
        return exec('indexer --rotate --all');
    }

    /**
     * @return string
     */
    public function userDeltaIndexAction()
    {
        return exec('indexer usersIndexDelta --rotate');
    }

    /**
     * @return string
     */
    public function pagesDeltaIndexAction()
    {
        return exec('indexer usersIndexDelta --rotate');
    }
}

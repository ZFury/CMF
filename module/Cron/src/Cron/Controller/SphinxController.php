<?php
/**
 *  Cron\Controller
 */
namespace Cron\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class SphinxController
 * @package Cron\Controller
 */
class SphinxController extends AbstractActionController
{
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
    public function pagesDeltaIndexAction()
    {
        return exec('indexer pagesIndexDelta --rotate');
    }

    /**
     * @return string
     */
    public function usersDeltaIndexAction()
    {
        return exec('indexer usersIndexDelta --rotate');
    }
}

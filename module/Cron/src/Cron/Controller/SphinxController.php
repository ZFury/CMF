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
        return system('indexer --rotate --all');
    }

    /**
     * @return string
     */
    public function pagesDeltaIndexAction()
    {
        return system('indexer pagesIndexDelta --rotate');
    }

    /**
     * @return string
     */
    public function usersDeltaIndexAction()
    {
        return system('indexer usersIndexDelta --rotate');
    }

    /**
     * @return string
     */
    public function rotateCustomIndexAction()
    {
        $sphinxIndex = $this->params('index');

        $query = "indexer $sphinxIndex --rotate";

        return system($query);
    }
}

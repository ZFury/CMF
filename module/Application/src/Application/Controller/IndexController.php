<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use SphinxSearch\Search;
use SphinxSearch\Db\Sql\Select;
use SphinxSearch\Db\Sql\Predicate\Match;
use SphinxSearch\Db\Adapter;
use SphinxSearch\Db\Adapter\Platform\SphinxQL;
use Zend\Db\Adapter\Adapter as ZendDBAdapter;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function testAction()
    {
//        $config = $this->getServiceLocator()->get('config');
//        $platform = new SphinxQL();
//        $adapter = new ZendDBAdapter($config['sphinxql'], $platform);

        $adapter = $this->getServiceLocator()->get('SphinxSearch\Db\Adapter\Adapter');

        $search = new Search($adapter);

//        $rowset = $search->search('example', new Match('asd*'));
        $rowset = $search->search('usersIndex', new Match('uniq*'));


        $search = new Search($adapter);
//        $rowset = $search->search('usersIndex', function(Select $select) {
//            $select->where(new Match('?', ''))
//                ->where(array('status = ?' => 'active'))
//                ;
//        });

        $rowset = $search->search('usersIndex', function(Select $select) {
            $select->where(new Match('?', 'admin*'))
                ->where(array('status = ?' => 'active'));
        });


        echo 'Founds row:' . PHP_EOL;
        foreach ($rowset as $row) {
            echo $row['id'] . PHP_EOL;
            var_dump($row);
        }

        var_dump($rowset->count());
        var_dump($rowset);

        die();
        return new ViewModel();
    }
}

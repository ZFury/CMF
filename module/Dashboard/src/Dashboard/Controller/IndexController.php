<?php
/**
 * Created by PhpStorm.
 * User: alexander
 * Date: 11/26/14
 * Time: 2:35 PM
 */

namespace Dashboard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $this->layout('layout/dashboard/dashboard');
//        /** @var \Doctrine\ORM\EntityManager $entityManager */
//        $entityManager = $this
//            ->getServiceLocator()
//            ->get('Doctrine\ORM\EntityManager');
//        $repository = $entityManager->getRepository('User\Entity\User');
//        $limit = 5;
//        $users = array_reverse($repository->findBy([], ['created' => 'DESC'], $limit));
//
//        return new ViewModel(['users' => $users]);
    }

    public function chartAction()
    {
        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');

        $emConfig = $entityManager->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');

        $queryBuilder = $entityManager->createQueryBuilder();
        $data = $queryBuilder->select('DATE(u.created) AS created_date, count(u.id) AS quantity')
            ->from('User\Entity\User', 'u')
            ->groupBy('created_date')->getQuery()
            ->execute();

        $cols = [
            ["id" => "", "label" => "Time", "pattern" => "", "type" => "date"],
            ["id" => "", "label" => "Users", "pattern" => "", "type" => "number"]
        ];
        $rows = [];
        foreach ($data as $row) {
            $temp = [];
            $date = new \DateTime($row['created_date']);
            $temp[] = ['v' => 'Date(' . $date->getTimestamp() . '000)', "f" => null];
            $temp[] = ['v' => (int)$row['quantity'], "f" => null];
            $rows[] = ['c' => $temp];
        }
        $table['cols'] = $cols;
        $table['rows'] = $rows;

        return new JsonModel($table);
    }

    public function tableAction()
    {
        $entityManager = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');
        $repository = $entityManager->getRepository('User\Entity\User');
        $limit = 5;
        $users = array_reverse($repository->findBy([], ['created' => 'DESC'], $limit));

        $cols = [
            ["id" => "", "label" => "Id", "pattern" => "", "type" => "number"],
            ["id" => "", "label" => "E-Mail", "pattern" => "", "type" => "string"],
            ["id" => "", "label" => "Displayed name", "pattern" => "", "type" => "string"],
            ["id" => "", "label" => "Role", "pattern" => "", "type" => "string"],
            ["id" => "", "label" => "Status", "pattern" => "", "type" => "string"],
            ["id" => "", "label" => "Created at", "pattern" => "", "type" => "date"]
        ];
        $rows = [];
        foreach ($users as $row) {
            $temp = [];
            $temp[] = ['v' => (int)$row->getId(), "f" => null];
            $temp[] = ['v' => $row->getEmail(), "f" => null];
            $temp[] = ['v' => $row->getDisplayName(), "f" => null];
            $temp[] = ['v' => $row->getRole(), "f" => null];
            $temp[] = ['v' => $row->getStatus(), "f" => null];
            $temp[] = ['v' => 'Date(' . $row->getCreated()->getTimestamp() . '000)', "f" => null];
            $rows[] = ['c' => $temp];
        }
        $table['cols'] = $cols;
        $table['rows'] = $rows;

        return new JsonModel($table);
    }
}

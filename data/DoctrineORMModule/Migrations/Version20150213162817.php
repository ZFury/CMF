<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150213162817 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('users', 'u')
            ->where("u.email = :email")
            ->setParameter(':email', 'admin@zfury.com');
        $admin = $query->execute()->fetch();

        $this->skipIf($admin);

        $date = date("Y-m-d H:i:s");

        if (!$admin) {
            $this->connection->createQueryBuilder()
                ->insert('users')
                ->values([
                    'email' => '?',
                    'displayName' => '?',
                    'role' => '?',
                    'confirm' => '?',
                    'status' => '?',
                    'created' => '?',
                    'updated' => '?'
                ])
                ->setParameter(0, 'admin@zfury.com')
                ->setParameter(1, 'admin@zfury.com')
                ->setParameter(2, 'admin')
                ->setParameter(3, null)
                ->setParameter(4, 'active')
                ->setParameter(5, $date)
                ->setParameter(6, $date)
                ->execute();

            $query = $this->connection->createQueryBuilder()
                ->select('*')
                ->from('users', 'u')
                ->where("u.email = :email")
                ->setParameter('email', 'admin@zfury.com');
            $admin = $query->execute()->fetch();

            $this->connection->createQueryBuilder()
                ->insert('auth')
                ->values([
                    'provider' => '?',
                    'userId' => '?',
                    'foreignKey' => '?',
                    'token' => '?',
                    'tokenSecret' => '?',
                    'tokenType' => '?',
                    'created' => '?',
                    'updated' => '?'
                ])
                ->setParameter(0, 'equals')
                ->setParameter(1, $admin['id'])
                ->setParameter(2, 'admin@zfury.com')
                ->setParameter(3, '$2y$10$YzZlNzQwZTk3ODAxYWJmYu3IvLTtSE92FDdeTQlwS1AFvtY55BHQO')
                ->setParameter(4, 'c6e740e97801abfcb498cc227db57f1b')
                ->setParameter(5, 'access')
                ->setParameter(6, $date)
                ->setParameter(7, $date)
                ->execute();
        }
    }

    public function down(Schema $schema)
    {
//        // this down() migration is auto-generated, please modify it to your needs
//        $this->connection->createQueryBuilder()
//            ->delete('auth', 'a')
//            ->where("a.foreignKey = :email")
//            ->setParameter(':email', 'admin@zfury.com')
//            ->execute();

        $this->connection->createQueryBuilder()
            ->delete('users', 'u')
            ->where("u.email = :email")
            ->setParameter('email', 'admin@zfury.com')
            ->execute();
    }
}

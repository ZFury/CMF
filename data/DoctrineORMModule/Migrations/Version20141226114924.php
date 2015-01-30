<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141226114924 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('entity_type', 'et')
            ->where("et.alias = :alias")
            ->setParameter('alias', 'test');
        $entityType = $query->execute()->fetch();

        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('test', 't')
            ->where("t.name = :name")
            ->setParameter('name', 'testuser');
        $testUser = $query->execute()->fetch();

        $this->skipIf(false !== $testUser && false !== $entityType);

        $entity = 'Test\Entity\Test';
        $date = date("Y-m-d H:i:s");

        if (false === $entityType) {
            $this->connection->createQueryBuilder()
                ->insert('entity_type')
                ->values([
                    'alias' => '?',
                    'entity' => '?',
                    'description' => '?',
                    'is_visible' => '?',
                    'is_enabled' => '?',
                    'created' => '?',
                    'updated' => '?'
                ])
                ->setParameter(0, 'test')
                ->setParameter(1, $entity)
                ->setParameter(2, 'Comment to a test entity')
                ->setParameter(3, 1)
                ->setParameter(4, 1)
                ->setParameter(5, $date)
                ->setParameter(6, $date)
                ->execute();
        }

        if (false === $testUser) {
            $this->connection->createQueryBuilder()
                ->insert('test')
                ->values([
                    'email' => '?',
                    'name' => '?'
                ])
                ->setParameter(0, 'testemailfor@testuser.com')
                ->setParameter(1, 'testuser')
                ->execute();
        }
    }

    public function down(Schema $schema)
    {
        $this->connection->createQueryBuilder()
            ->delete('entity_type', 'et')
            ->where("et.alias = :alias")
            ->setParameter('alias', 'test')
            ->execute();
    }
}

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
        $entity = addslashes('Test\Entity\Test');
        $date = date("Y-m-d H:i:s");
        $this->addSql(
            "INSERT INTO entity_type (
            aliasEntity,
            entity,
            description,
            visibleComment,
            enabledComment,
            created,
            updated) VALUES ('test','" . $entity . "' , 'Comment to a test entity', 1, 1,'" . $date . "', '" . $date . "')"
        );
        $this->addSql(
            "INSERT INTO test (email, name) VALUES('testemailfor@testuser.com','testuser')"
        );
    }

    public function down(Schema $schema)
    {
        $this->addSql("TRUNCATE TABLE entity_type");
    }
}

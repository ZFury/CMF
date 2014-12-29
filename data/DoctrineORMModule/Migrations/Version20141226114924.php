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
        $entity = addslashes('Comment\Entity\Comment');
        $date = date("Y-m-d H:i:s");
        $this->addSql(
            "insert into entity_type (aliasEntity, entity, description, visibleComment, enabledComment,
 created, updated) VALUES('comment','" . $entity . "' , 'Comment to comment', 1, 1,'" . $date . "', '" .
            $date . "')"
        );
    }

    public function down(Schema $schema)
    {
        $this->addSql("TRUNCATE TABLE entity_type");
    }
}

<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141229130437 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO `mail_templates`(`id`,`alias`,`description`,`subject`,`fromEmail`,`fromName`,`bodyHtml`,`bodyText`,`created`,`updated`)
            VALUES (null,'sign-up','Template for registration','Congratulation','ZFStarter@admins.com','Admin ZFStarter','<p>Please confirm your registration <a href=\"%confirm%\">confirm</a></p>','Please confirm your registration  %confirm%','2014-12-16 13:04:59','2014-12-17 22:29:46');");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("DELETE FROM mail_templates WHERE alias = 'sign-up' LIMIT 1;");
    }
}

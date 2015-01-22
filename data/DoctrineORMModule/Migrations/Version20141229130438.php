<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141229130438 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO `mail_templates` (`id`, `alias`, `description`, `subject`, `fromEmail`, `fromName`,
 `bodyHtml`, `bodyText`, `created`, `updated`)
        VALUES (NULL, 'forgot-password', 'Template for recovery password', 'Password recovery', 'ZFury@admins.com',
         'Admin ZFury', 'Hello User,
          To reset account password click on the following link or copy-paste it in your browser:<span></span><p>
          <a href=\"%reset-password-link%\">%reset-password-link%</a><br></p>', 'Hello User, To reset account password
           click on the following link or copy-paste it in your browser:â€‹\r\n%reset-password-link%',
            '2014-12-20 14:57:07', '2014-12-20 14:57:07');");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM mail_templates WHERE alias = 'forgot-password' LIMIT 1;");
    }
}

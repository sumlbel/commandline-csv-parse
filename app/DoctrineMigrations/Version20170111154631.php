<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20170111154631
 *
 * @package Application\Migrations
 */
class Version20170111154631 extends AbstractMigration
{
    /**
     * Migrate to this version
     *
     * @param Schema $schema Argument of Doctrine Migrations
     *
     * @return void
     */
    public function up(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !==
            'mysql', 'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'ALTER TABLE tblProductData '.
            'CHANGE stock stock INT UNSIGNED DEFAULT 0 NOT NULL, '.
            'CHANGE price price NUMERIC(6, 2) NOT NULL, '.
            'CHANGE dtmAdded dtmAdded DATETIME DEFAULT CURRENT_TIMESTAMP, '.
            'CHANGE stmTimeStamp stmTimeStamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL'
        );
    }

    /**
     * Revert to previous version
     *
     * @param Schema $schema Argument of Doctrine Migrations
     *
     * @return void
     */
    public function down(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !==
            'mysql', 'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'ALTER TABLE tblProductData '.
            'CHANGE stock stock INT NOT NULL, '.
            'CHANGE price price DOUBLE PRECISION NOT NULL, '.
            'CHANGE dtmAdded dtmAdded DATETIME DEFAULT NULL, '.
            'CHANGE stmTimeStamp stmTimeStamp DATETIME NOT NULL'
        );
    }
}

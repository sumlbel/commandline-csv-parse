<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Version 20170110081253 of Doctrine Migrations
 *
 * @package Application\Migrations
 */
class Version20170110081253 extends AbstractMigration
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
            'ALTER TABLE tblProductData ADD stock INT NOT NULL, '.
            'ADD price DOUBLE PRECISION NOT NULL, '.
            'CHANGE intProductDataId intProductDataId INT AUTO_INCREMENT NOT NULL, '.
            'CHANGE stmTimestamp stmTimeStamp DATETIME NOT NULL'
        );
        $this->addSql(
            'ALTER TABLE tblProductData '.
            'RENAME INDEX strproductcode TO UNIQ_2C11248662F10A58'
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
            'ALTER TABLE tblProductData DROP stock, DROP price, '.
            'CHANGE intProductDataId intProductDataId '.
            'INT UNSIGNED AUTO_INCREMENT NOT NULL, '.
            'CHANGE stmTimeStamp stmTimestamp '.
            'DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL'
        );
        $this->addSql(
            'ALTER TABLE tblProductData '.
            'RENAME INDEX uniq_2c11248662f10a58 TO strProductCode'
        );
    }
}


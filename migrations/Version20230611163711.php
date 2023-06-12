<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230611163711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Generates base tables "orders" and "vouchers"';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            '
            CREATE TABLE orders (
            id INT AUTO_INCREMENT NOT NULL,
            uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            amount DOUBLE PRECISION NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            UNIQUE INDEX UNIQ_E52FFDEED17F50A6 (uuid),
            INDEX created_at_idx (created_at),
            PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            '
        );

        $this->addSql(
            '
            CREATE TABLE vouchers (
            id INT AUTO_INCREMENT NOT NULL,
            uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
            order_id INT DEFAULT NULL,
            type ENUM(\'CONCRETE\', \'PERCENTAGE\') NOT NULL,
            discount DOUBLE PRECISION NOT NULL,
            expiration_date DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            UNIQUE INDEX UNIQ_93150748D17F50A6 (uuid),
            UNIQUE INDEX UNIQ_931507488D9F6D38 (order_id),
            INDEX expiration_date_idx (expiration_date),
            INDEX is_active_idx (order_id, expiration_date),
            PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            '
        );

        $this->addSql(
            '
            ALTER TABLE vouchers
            ADD CONSTRAINT FK_931507488D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE
            '
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE vouchers DROP FOREIGN KEY FK_931507488D9F6D38');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE vouchers');
    }
}

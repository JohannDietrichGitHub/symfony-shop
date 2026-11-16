<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261116120306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "order" ADD currency VARCHAR(3) NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD stripe_checkout_session_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "order" ADD stripe_payment_intent_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "order" ADD paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F52993985A18FBC7 ON "order" (stripe_checkout_session_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F5299398FC72F97E ON "order" (stripe_payment_intent_id)');
        $this->addSql('ALTER TABLE order_item ADD product_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product ADD archived_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_F52993985A18FBC7');
        $this->addSql('DROP INDEX UNIQ_F5299398FC72F97E');
        $this->addSql('ALTER TABLE "order" DROP currency');
        $this->addSql('ALTER TABLE "order" DROP stripe_checkout_session_id');
        $this->addSql('ALTER TABLE "order" DROP stripe_payment_intent_id');
        $this->addSql('ALTER TABLE "order" DROP paid_at');
        $this->addSql('ALTER TABLE order_item DROP product_name');
        $this->addSql('ALTER TABLE product DROP archived_at');
    }
}

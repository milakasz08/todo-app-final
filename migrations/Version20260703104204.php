<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migrates the "type" column of the "resource" table from free-text,
 * Polish-language values ("Książka", "Płyta", "Film") to stable,
 * language-independent constants used by the App\Enum\MediaType enum
 * ("ksiazka", "plyta", "film"). This makes filtering reliable and
 * independent from the application's locale.
 */
final class Version20260703104204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convert resource.type free-text values to MediaType enum constants.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE resource SET type = 'ksiazka' WHERE LOWER(type) IN ('książka', 'ksiazka', 'book')");
        $this->addSql("UPDATE resource SET type = 'film' WHERE LOWER(type) IN ('film', 'dvd')");
        $this->addSql("UPDATE resource SET type = 'plyta' WHERE LOWER(type) IN ('płyta', 'plyta', 'cd', 'audio', 'record')");
        // Wszystko, co nie pasuje do żadnego z powyższych, domyślnie traktujemy jako książkę.
        $this->addSql("UPDATE resource SET type = 'ksiazka' WHERE type NOT IN ('ksiazka', 'film', 'plyta')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE resource SET type = 'Książka' WHERE type = 'ksiazka'");
        $this->addSql("UPDATE resource SET type = 'Film' WHERE type = 'film'");
        $this->addSql("UPDATE resource SET type = 'Płyta' WHERE type = 'plyta'");
    }
}

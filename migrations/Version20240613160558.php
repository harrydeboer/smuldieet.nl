<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240613160558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, recipe_id INT DEFAULT NULL, page_id INT DEFAULT NULL, created_at BIGINT NOT NULL, updated_at BIGINT DEFAULT NULL, content LONGTEXT NOT NULL, pending TINYINT(1) NOT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526C59D8A214 (recipe_id), INDEX IDX_9474526CC4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cookbook (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created_at BIGINT NOT NULL, updated_at BIGINT DEFAULT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_BB59C3C3A76ED395 (user_id), UNIQUE INDEX title_unique (user_id, title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cookbook_recipe_weight (id BIGINT AUTO_INCREMENT NOT NULL, cookbook_id INT NOT NULL, recipe_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_E67D77AE7C8804F (cookbook_id), INDEX IDX_E67D77AE59D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE day (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, timestamp BIGINT DEFAULT NULL, INDEX IDX_E5A02990A76ED395 (user_id), UNIQUE INDEX timestamp_unique (user_id, timestamp), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE day_foodstuff_weight (id BIGINT AUTO_INCREMENT NOT NULL, day_id INT NOT NULL, foodstuff_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, INDEX IDX_50E930599C24126 (day_id), INDEX IDX_50E93059AEDD2A05 (foodstuff_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE day_recipe_weight (id BIGINT AUTO_INCREMENT NOT NULL, day_id INT NOT NULL, recipe_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_DA3D22209C24126 (day_id), INDEX IDX_DA3D222059D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE foodstuff (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, created_at BIGINT NOT NULL, updated_at BIGINT DEFAULT NULL, name VARCHAR(255) NOT NULL, piece_name VARCHAR(255) DEFAULT NULL, pieces_name VARCHAR(255) DEFAULT NULL, piece_weight DOUBLE PRECISION DEFAULT NULL, liquid TINYINT(1) NOT NULL, density DOUBLE PRECISION DEFAULT NULL, energy DOUBLE PRECISION DEFAULT NULL, water DOUBLE PRECISION DEFAULT NULL, protein DOUBLE PRECISION DEFAULT NULL, carbohydrates DOUBLE PRECISION DEFAULT NULL, sucre DOUBLE PRECISION DEFAULT NULL, fat DOUBLE PRECISION DEFAULT NULL, saturated_fat DOUBLE PRECISION DEFAULT NULL, monounsaturated_fat DOUBLE PRECISION DEFAULT NULL, polyunsaturated_fat DOUBLE PRECISION DEFAULT NULL, cholesterol DOUBLE PRECISION DEFAULT NULL, dietary_fiber DOUBLE PRECISION DEFAULT NULL, salt DOUBLE PRECISION DEFAULT NULL, vitamin_a DOUBLE PRECISION DEFAULT NULL, vitamin_b1 DOUBLE PRECISION DEFAULT NULL, vitamin_b2 DOUBLE PRECISION DEFAULT NULL, vitamin_b3 DOUBLE PRECISION DEFAULT NULL, vitamin_b6 DOUBLE PRECISION DEFAULT NULL, vitamin_b11 DOUBLE PRECISION DEFAULT NULL, vitamin_b12 DOUBLE PRECISION DEFAULT NULL, vitamin_c DOUBLE PRECISION DEFAULT NULL, vitamin_d DOUBLE PRECISION DEFAULT NULL, vitamin_e DOUBLE PRECISION DEFAULT NULL, vitamin_k DOUBLE PRECISION DEFAULT NULL, potassium DOUBLE PRECISION DEFAULT NULL, calcium DOUBLE PRECISION DEFAULT NULL, phosphorus DOUBLE PRECISION DEFAULT NULL, iron DOUBLE PRECISION DEFAULT NULL, magnesium DOUBLE PRECISION DEFAULT NULL, copper DOUBLE PRECISION DEFAULT NULL, zinc DOUBLE PRECISION DEFAULT NULL, selenium DOUBLE PRECISION DEFAULT NULL, iodine DOUBLE PRECISION DEFAULT NULL, manganese DOUBLE PRECISION DEFAULT NULL, molybdenum DOUBLE PRECISION DEFAULT NULL, chromium DOUBLE PRECISION DEFAULT NULL, fluoride DOUBLE PRECISION DEFAULT NULL, alcohol DOUBLE PRECISION DEFAULT NULL, caffeine DOUBLE PRECISION DEFAULT NULL, INDEX IDX_B14320F4A76ED395 (user_id), UNIQUE INDEX name_unique (user_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nutrient (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, min_rda DOUBLE PRECISION DEFAULT NULL, max_rda DOUBLE PRECISION DEFAULT NULL, unit VARCHAR(255) NOT NULL, decimal_places INT NOT NULL, UNIQUE INDEX UNIQ_A9962C5A5E237E06 (name), UNIQUE INDEX UNIQ_A9962C5AD5499347 (display_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created_at BIGINT NOT NULL, updated_at BIGINT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, summary VARCHAR(255) DEFAULT NULL, content LONGTEXT NOT NULL, INDEX IDX_140AB620A76ED395 (user_id), UNIQUE INDEX title_unique (title), UNIQUE INDEX slug_unique (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profanity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1ABCAD55E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, recipe_id INT DEFAULT NULL, created_at BIGINT NOT NULL, updated_at BIGINT DEFAULT NULL, content LONGTEXT DEFAULT NULL, rating INT NOT NULL, pending TINYINT(1) NOT NULL, INDEX IDX_D8892622A76ED395 (user_id), INDEX IDX_D889262259D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created_at BIGINT NOT NULL, updated_at BIGINT DEFAULT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, ingredients LONGTEXT NOT NULL, preparation_method LONGTEXT NOT NULL, number_of_persons INT NOT NULL, rating DOUBLE PRECISION DEFAULT NULL, votes INT NOT NULL, times_saved INT NOT NULL, times_reacted INT NOT NULL, cooking_time VARCHAR(255) NOT NULL, kitchen VARCHAR(255) NOT NULL, occasion VARCHAR(255) DEFAULT NULL, type_of_dish VARCHAR(255) NOT NULL, self_invented TINYINT(1) NOT NULL, source VARCHAR(255) DEFAULT NULL, pending TINYINT(1) NOT NULL, image_extension VARCHAR(255) DEFAULT NULL, vegetarian TINYINT(1) NOT NULL, vegan TINYINT(1) NOT NULL, histamine_free TINYINT(1) NOT NULL, cow_milk_free TINYINT(1) NOT NULL, soy_free TINYINT(1) NOT NULL, gluten_free TINYINT(1) NOT NULL, chicken_egg_protein_free TINYINT(1) NOT NULL, nut_free TINYINT(1) NOT NULL, without_packages_and_bags TINYINT(1) NOT NULL, INDEX IDX_DA88B137A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_foodstuff_weight (id BIGINT AUTO_INCREMENT NOT NULL, recipe_id INT NOT NULL, foodstuff_id INT NOT NULL, value DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, INDEX IDX_9793FB7E59D8A214 (recipe_id), INDEX IDX_9793FB7EAEDD2A05 (foodstuff_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, created_at BIGINT NOT NULL, updated_at BIGINT DEFAULT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_389B7835E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_recipe (tag_id INT NOT NULL, recipe_id INT NOT NULL, INDEX IDX_33C9F81BBAD26311 (tag_id), INDEX IDX_33C9F81B59D8A214 (recipe_id), PRIMARY KEY(tag_id, recipe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, created_at BIGINT NOT NULL, updated_at BIGINT DEFAULT NULL, first_name VARCHAR(180) DEFAULT NULL, last_name VARCHAR(180) DEFAULT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, gender VARCHAR(255) NOT NULL, weight DOUBLE PRECISION NOT NULL, birth_time BIGINT NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, verified TINYINT(1) NOT NULL, image_extension VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_saved_recipe (user_id INT NOT NULL, recipe_id INT NOT NULL, INDEX IDX_3E42B62EA76ED395 (user_id), INDEX IDX_3E42B62E59D8A214 (recipe_id), PRIMARY KEY(user_id, recipe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CC4663E4 FOREIGN KEY (page_id) REFERENCES page (id)');
        $this->addSql('ALTER TABLE cookbook ADD CONSTRAINT FK_BB59C3C3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cookbook_recipe_weight ADD CONSTRAINT FK_E67D77AE7C8804F FOREIGN KEY (cookbook_id) REFERENCES cookbook (id)');
        $this->addSql('ALTER TABLE cookbook_recipe_weight ADD CONSTRAINT FK_E67D77AE59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE day ADD CONSTRAINT FK_E5A02990A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE day_foodstuff_weight ADD CONSTRAINT FK_50E930599C24126 FOREIGN KEY (day_id) REFERENCES day (id)');
        $this->addSql('ALTER TABLE day_foodstuff_weight ADD CONSTRAINT FK_50E93059AEDD2A05 FOREIGN KEY (foodstuff_id) REFERENCES foodstuff (id)');
        $this->addSql('ALTER TABLE day_recipe_weight ADD CONSTRAINT FK_DA3D22209C24126 FOREIGN KEY (day_id) REFERENCES day (id)');
        $this->addSql('ALTER TABLE day_recipe_weight ADD CONSTRAINT FK_DA3D222059D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE foodstuff ADD CONSTRAINT FK_B14320F4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB620A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D889262259D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recipe_foodstuff_weight ADD CONSTRAINT FK_9793FB7E59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_foodstuff_weight ADD CONSTRAINT FK_9793FB7EAEDD2A05 FOREIGN KEY (foodstuff_id) REFERENCES foodstuff (id)');
        $this->addSql('ALTER TABLE tag_recipe ADD CONSTRAINT FK_33C9F81BBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_recipe ADD CONSTRAINT FK_33C9F81B59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_saved_recipe ADD CONSTRAINT FK_3E42B62EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_saved_recipe ADD CONSTRAINT FK_3E42B62E59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C59D8A214');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CC4663E4');
        $this->addSql('ALTER TABLE cookbook DROP FOREIGN KEY FK_BB59C3C3A76ED395');
        $this->addSql('ALTER TABLE cookbook_recipe_weight DROP FOREIGN KEY FK_E67D77AE7C8804F');
        $this->addSql('ALTER TABLE cookbook_recipe_weight DROP FOREIGN KEY FK_E67D77AE59D8A214');
        $this->addSql('ALTER TABLE day DROP FOREIGN KEY FK_E5A02990A76ED395');
        $this->addSql('ALTER TABLE day_foodstuff_weight DROP FOREIGN KEY FK_50E930599C24126');
        $this->addSql('ALTER TABLE day_foodstuff_weight DROP FOREIGN KEY FK_50E93059AEDD2A05');
        $this->addSql('ALTER TABLE day_recipe_weight DROP FOREIGN KEY FK_DA3D22209C24126');
        $this->addSql('ALTER TABLE day_recipe_weight DROP FOREIGN KEY FK_DA3D222059D8A214');
        $this->addSql('ALTER TABLE foodstuff DROP FOREIGN KEY FK_B14320F4A76ED395');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620A76ED395');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622A76ED395');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D889262259D8A214');
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137A76ED395');
        $this->addSql('ALTER TABLE recipe_foodstuff_weight DROP FOREIGN KEY FK_9793FB7E59D8A214');
        $this->addSql('ALTER TABLE recipe_foodstuff_weight DROP FOREIGN KEY FK_9793FB7EAEDD2A05');
        $this->addSql('ALTER TABLE tag_recipe DROP FOREIGN KEY FK_33C9F81BBAD26311');
        $this->addSql('ALTER TABLE tag_recipe DROP FOREIGN KEY FK_33C9F81B59D8A214');
        $this->addSql('ALTER TABLE user_saved_recipe DROP FOREIGN KEY FK_3E42B62EA76ED395');
        $this->addSql('ALTER TABLE user_saved_recipe DROP FOREIGN KEY FK_3E42B62E59D8A214');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE cookbook');
        $this->addSql('DROP TABLE cookbook_recipe_weight');
        $this->addSql('DROP TABLE day');
        $this->addSql('DROP TABLE day_foodstuff_weight');
        $this->addSql('DROP TABLE day_recipe_weight');
        $this->addSql('DROP TABLE foodstuff');
        $this->addSql('DROP TABLE nutrient');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE profanity');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE recipe_foodstuff_weight');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_recipe');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_saved_recipe');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

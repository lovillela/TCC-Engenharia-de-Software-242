<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421042340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        /*$this->addSql(<<<SQL
        SQL);*/

        /*$this->addSql(<<<SQL
        CREATE DATABASE IF NOT EXISTS `blog_app` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
        USE `blog_app`;
        SQL);*/

        $this->addSql(<<<SQL
        CREATE TABLE IF NOT EXISTS `category` (
            `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `description` longtext NOT NULL,
            `slug` varchar(512) NOT NULL,
            PRIMARY KEY (`id`) USING BTREE,
            UNIQUE KEY `slug` (`slug`) USING BTREE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        SQL);

        $this->addSql(<<<SQL
        CREATE TABLE IF NOT EXISTS `category_tag` (
            `id_category` mediumint UNSIGNED NOT NULL,
            `id_tag` mediumint UNSIGNED NOT NULL,
            PRIMARY KEY (`id_category`,`id_tag`) USING BTREE,
            KEY `tag_id_FK-category_tag` (`id_tag`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        SQL);

        $this->addSql(<<<SQL
        CREATE TABLE IF NOT EXISTS `post` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `slug` varchar(512) NOT NULL,
            PRIMARY KEY (`id`) USING BTREE,
            UNIQUE KEY `slug_post_unique` (`slug`) USING BTREE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        SQL);

        $this->addSql(<<<SQL
        CREATE TABLE IF NOT EXISTS `post_category` (
            `id_post` bigint UNSIGNED NOT NULL,
            `id_category` mediumint UNSIGNED NOT NULL,
            PRIMARY KEY (`id_post`,`id_category`) USING BTREE,
            KEY `category_id_FK` (`id_category`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        SQL);

        $this->addSql(<<<SQL
        CREATE TABLE IF NOT EXISTS `post_tag` (
            `id_post` bigint UNSIGNED NOT NULL,
            `id_tag` mediumint UNSIGNED NOT NULL,
            PRIMARY KEY (`id_post`,`id_tag`) USING BTREE,
            KEY `tag_id_FK-post_tag` (`id_tag`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        SQL);

        $this->addSql(<<<SQL
        CREATE TABLE IF NOT EXISTS `post_users` (
            `id_user` bigint UNSIGNED NOT NULL,
            `id_post` bigint UNSIGNED NOT NULL,
            PRIMARY KEY (`id_user`,`id_post`),
            KEY `post_id_FK` (`id_post`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        SQL);

        $this->addSql(<<<SQL
        CREATE TABLE IF NOT EXISTS `slug_map` (
            `entity_id` bigint UNSIGNED NOT NULL,
            `entity_type` enum('post','category','tag') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `slug` varchar(512) NOT NULL,
            UNIQUE KEY `UNIQUE_slug-slug_map` (`slug`) USING BTREE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        SQL);

        $this->addSql(<<<SQL
        CREATE TABLE IF NOT EXISTS `tag` (
            `id` mediumint UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` varchar(512) NOT NULL,
            `slug` varchar(512) NOT NULL,
            PRIMARY KEY (`id`) USING BTREE,
            UNIQUE KEY `slug` (`slug`) USING BTREE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        SQL);

        $this->addSql(<<<SQL
        CREATE TABLE IF NOT EXISTS `users` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `username` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `email` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `permissions` int UNSIGNED NOT NULL,
            `password` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
            `isActive` tinyint NOT NULL,
            PRIMARY KEY (`id`) USING BTREE,
            UNIQUE KEY `userName_Unique` (`username`) USING BTREE,
            UNIQUE KEY `email_Unique` (`email`) USING BTREE,
            KEY `active_user` (`isActive`) USING BTREE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        SQL);

        $this->addSql(<<<SQL
        CREATE TABLE IF NOT EXISTS `user_comment_post` (
            `id` bigint UNSIGNED NOT NULL,
            `id_user` bigint UNSIGNED NOT NULL,
            `id_post` bigint UNSIGNED NOT NULL,
            `content` mediumtext NOT NULL,
            `parent` bigint UNSIGNED DEFAULT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `is_visible` tinyint NOT NULL DEFAULT '1',
            PRIMARY KEY (`id`) USING BTREE,
            KEY `user_id-user_comment_post` (`id_user`),
            KEY `post_id-user_comment_post` (`id_post`),
            KEY `comment_id-user_comment_post` (`parent`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        SQL);

        $this->addSql(<<<SQL
        CREATE TABLE IF NOT EXISTS `user_reaction_post` (
            `id_user` bigint UNSIGNED NOT NULL,
            `id_post` bigint UNSIGNED NOT NULL,
            `reaction` tinyint(1) NOT NULL,
            PRIMARY KEY (`id_user`,`id_post`) USING BTREE,
            KEY `post_id-user_like_post` (`id_post`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        SQL);

        $this->addSql(<<<SQL
        ALTER TABLE `category` ADD FULLTEXT KEY `title_description_INDEX` (`description`,`title`);
        SQL);

        $this->addSql(<<<SQL
        ALTER TABLE `post` ADD FULLTEXT KEY `title_content` (`title`,`content`);
        SQL);

        $this->addSql(<<<SQL
        ALTER TABLE `user_comment_post` ADD FULLTEXT KEY `comment_content` (`content`);
        SQL);

        $this->addSql(<<<SQL
        ALTER TABLE `category_tag`
        ADD CONSTRAINT `category_id_FK-category_tag` FOREIGN KEY (`id_category`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        ADD CONSTRAINT `tag_id_FK-category_tag` FOREIGN KEY (`id_tag`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        SQL);

        $this->addSql(<<<SQL
        ALTER TABLE `post_category`
        ADD CONSTRAINT `category_id_FK` FOREIGN KEY (`id_category`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        ADD CONSTRAINT `post_id_FK-post_category` FOREIGN KEY (`id_post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        SQL);
        
        $this->addSql(<<<SQL
        ALTER TABLE `post_tag`
        ADD CONSTRAINT `post_id_FK-post_tag` FOREIGN KEY (`id_post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        ADD CONSTRAINT `tag_id_FK-post_tag` FOREIGN KEY (`id_tag`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        SQL);
        
        $this->addSql(<<<SQL
        ALTER TABLE `post_users`
        ADD CONSTRAINT `post_id_FK` FOREIGN KEY (`id_post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        ADD CONSTRAINT `user_id_FK` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        SQL);

        $this->addSql(<<<SQL
        ALTER TABLE `user_comment_post`
        ADD CONSTRAINT `comment_id-user_comment_post` FOREIGN KEY (`parent`) REFERENCES `user_comment_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        ADD CONSTRAINT `post_id-user_comment_post` FOREIGN KEY (`id_post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        ADD CONSTRAINT `user_id-user_comment_post` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);
        SQL);

        $this->addSql(<<<SQL
        ALTER TABLE `user_reaction_post`
        ADD CONSTRAINT `post_id-user_like_post` FOREIGN KEY (`id_post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        ADD CONSTRAINT `user_id-user_like_post` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        SQL);
        
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->addSql(<<<SQL
        //SQL);

        $this->addSql('ALTER TABLE `user_comment_post` DROP FOREIGN KEY `comment_id-user_comment_post`;');
        $this->addSql('ALTER TABLE `user_comment_post` DROP FOREIGN KEY `post_id-user_comment_post`;');
        $this->addSql('ALTER TABLE `user_comment_post` DROP FOREIGN KEY `user_id-user_comment_post`;');
        $this->addSql('ALTER TABLE `post_users` DROP FOREIGN KEY `post_id_FK`;');
        $this->addSql('ALTER TABLE `post_users` DROP FOREIGN KEY `user_id_FK`;');
        $this->addSql('ALTER TABLE `post_tag` DROP FOREIGN KEY `post_id_FK-post_tag`;');
        $this->addSql('ALTER TABLE `post_tag` DROP FOREIGN KEY `tag_id_FK-post_tag`;');
        $this->addSql('ALTER TABLE `post_category` DROP FOREIGN KEY `category_id_FK`;');
        $this->addSql('ALTER TABLE `post_category` DROP FOREIGN KEY `post_id_FK-post_category`;');
        $this->addSql('ALTER TABLE `category_tag` DROP FOREIGN KEY `category_id_FK-category_tag`;');
        $this->addSql('ALTER TABLE `category_tag` DROP FOREIGN KEY `tag_id_FK-category_tag`;');
    
        $this->addSql('DROP TABLE IF EXISTS `user_reaction_post`;');
        $this->addSql('DROP TABLE IF EXISTS `user_comment_post`;');
        $this->addSql('DROP TABLE IF EXISTS `users`;');
        $this->addSql('DROP TABLE IF EXISTS `tag`;');
        $this->addSql('DROP TABLE IF EXISTS `slug_map`;');
        $this->addSql('DROP TABLE IF EXISTS `post_users`;');
        $this->addSql('DROP TABLE IF EXISTS `post_tag`;');
        $this->addSql('DROP TABLE IF EXISTS `post_category`;');
        $this->addSql('DROP TABLE IF EXISTS `post`;');
        $this->addSql('DROP TABLE IF EXISTS `category_tag`;');
        $this->addSql('DROP TABLE IF EXISTS `category`;');
        $this->addSql('DROP DATABASE IF EXISTS `blog_app`;');
    }
}
-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           5.6.21 - MySQL Community Server (GPL)
-- OS do Servidor:               Win32
-- HeidiSQL Versão:              9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Copiando estrutura para tabela tss.credentials
DROP TABLE IF EXISTS `credentials`;
CREATE TABLE IF NOT EXISTS `credentials` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL,
	`type` smallint(6) NOT NULL,
	`value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`active` tinyint(1) NOT NULL,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	PRIMARY KEY (`id`),
	KEY `IDX_FA05280EA76ED395` (`user_id`),
	CONSTRAINT `FK_FA05280EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Copiando dados para a tabela tss.credentials: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `credentials` DISABLE KEYS */;
INSERT INTO `credentials` (`id`, `user_id`, `type`, `value`, `active`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, '7b2e9f54cdff413fcde01f330af6896c3cd7e6cd', 1, '2015-10-17 11:30:20', '2015-10-17 11:30:22');
/*!40000 ALTER TABLE `credentials` ENABLE KEYS */;


-- Copiando estrutura para tabela tss.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`active` tinyint(1) NOT NULL,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Copiando dados para a tabela tss.roles: ~2 rows (aproximadamente)
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `name`, `active`, `created_at`, `updated_at`) VALUES
	(1, 'Admin', 1, '2015-10-17 11:27:35', '2015-10-17 11:27:36'),
	(2, 'Member', 1, '2015-10-17 11:29:10', '2015-10-17 11:29:10');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;


-- Copiando estrutura para tabela tss.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`role_id` int(11) NOT NULL,
	`first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	`last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	`username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	`email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	`avatar` longtext COLLATE utf8_unicode_ci,
	`gender` smallint(6) DEFAULT NULL,
	`birthday` datetime DEFAULT NULL,
	`bio` longtext COLLATE utf8_unicode_ci,
	`status` smallint(6) NOT NULL,
	`active` tinyint(1) NOT NULL,
	`token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	`confirmed_email` tinyint(1) NOT NULL,
	`created_at` datetime NOT NULL,
	`updated_at` datetime NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQ_1483A5E9F85E0677` (`username`),
	UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`),
	KEY `IDX_1483A5E9D60322AC` (`role_id`),
	CONSTRAINT `FK_1483A5E9D60322AC` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Copiando dados para a tabela tss.users: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `role_id`, `first_name`, `last_name`, `username`, `email`, `avatar`, `gender`, `birthday`, `bio`, `status`, `active`, `token`, `confirmed_email`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Master', 'Root', 'admin', 'admin', NULL, NULL, NULL, NULL, 2, 1, NULL, 1, '2015-10-17 11:29:59', '2015-10-17 11:30:03');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

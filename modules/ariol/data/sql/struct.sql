CREATE TABLE `changelog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` int(10) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `config` (
  `group` varchar(128) NOT NULL,
  `key` varchar(128) NOT NULL,
  `value` text,
  PRIMARY KEY (`group`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(127) NOT NULL,
  `password` char(64) DEFAULT NULL,
  `roles` INT UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `logins` INT UNSIGNED NOT NULL DEFAULT '0',
  `last_login` INT UNSIGNED DEFAULT NULL,
  `login_attempts` INT UNSIGNED DEFAULT NULL,
  `banned_to` INT UNSIGNED DEFAULT NULL,
  `ban_time` INT UNSIGNED DEFAULT NULL,
  `reset_password_code` varchar(32) DEFAULT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  `updated_at` INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_tokens` (
  `id` INT unsigned NOT NULL AUTO_INCREMENT,
  `user_id` INT unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `created` INT unsigned NOT NULL,
  `expires` INT unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UN_user_tokens_token` (`token`),
  CONSTRAINT `FK_user_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
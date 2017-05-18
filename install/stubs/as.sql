CREATE TABLE IF NOT EXISTS `as_users` (
  `user_id` int(11) NOT NULL auto_increment,
  `email` varchar(40) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `confirmation_key` varchar(40) NOT NULL,
  `confirmed` enum('Y','N') NOT NULL default 'N',
  `password_reset_key` varchar(250) NOT NULL default '',
  `password_reset_confirmed` enum('Y','N') NOT NULL default 'N',
  `password_reset_timestamp` datetime,
  `register_date` date NOT NULL,
  `user_role` int(4) NOT NULL default 1,
  `last_login` datetime,
  `banned` enum('Y','N') NOT NULL default 'N',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `as_social_logins` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `provider` varchar(50) DEFAULT 'email',
    `provider_id` varchar(250) DEFAULT NULL,
    `created_at` datetime,
    PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `as_login_attempts` (
  `id_login_attempts` int(11) NOT NULL AUTO_INCREMENT,
  `ip_addr` varchar(20) NOT NULL,
  `attempt_number` int(11) NOT NULL DEFAULT '1',
  `date` date NOT NULL,
  PRIMARY KEY (`id_login_attempts`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `as_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `posted_by` int(11) NOT NULL,
  `posted_by_name` varchar(30) NOT NULL,
  `comment` text NOT NULL,
  `post_time` datetime NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `as_user_details` (
  `id_user_details` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(35) NOT NULL DEFAULT '',
  `last_name` varchar(35) NOT NULL DEFAULT '',
  `phone` varchar(30) NOT NULL DEFAULT '',
  `address` varchar(30) NOT NULL DEFAULT '',

  PRIMARY KEY (`id_user_details`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `as_user_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(20) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `as_user_roles` (`role_id`, `role`) VALUES
(1, 'user'),
(2, 'editor'),
(3, 'admin');
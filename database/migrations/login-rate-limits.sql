CREATE TABLE IF NOT EXISTS `login_rate_limits` (
  `ip_key` binary(64) NOT NULL,
  `expires_at` bigint unsigned NOT NULL,
  `failed_attempts` tinyint unsigned NOT NULL,
  `email_failures` json NOT NULL,
  PRIMARY KEY (`ip_key`),
  KEY `login_rate_limits_expiry` (`expires_at`)
) ENGINE=InnoDB;

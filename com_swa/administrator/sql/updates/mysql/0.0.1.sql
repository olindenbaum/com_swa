CREATE  TABLE IF NOT EXISTS `#__swa_university` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(200) NOT NULL ,
  `code` VARCHAR(10) NOT NULL ,
  `url` VARCHAR(200) NULL ,
  `password` VARCHAR(20) NOT NULL ,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` INT(11)  NOT NULL ,
  `state` TINYINT(1)  NOT NULL ,
  `checked_out` INT(11)  NOT NULL ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11)  NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
  UNIQUE INDEX `code_UNIQUE` (`code` ASC)
)
DEFAULT COLLATE=utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `#__swa_season` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `year` VARCHAR(4) NOT NULL ,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` INT(11)  NOT NULL ,
  `state` TINYINT(1)  NOT NULL ,
  `checked_out` INT(11)  NOT NULL ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11)  NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `year_UNIQUE` (`year` ASC)
)
DEFAULT COLLATE=utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `#__swa_deposit` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `university_id` INT NOT NULL ,
  `time` DATETIME NOT NULL ,
  `amount` DECIMAL NOT NULL ,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` INT(11)  NOT NULL ,
  `state` TINYINT(1)  NOT NULL ,
  `checked_out` INT(11)  NOT NULL ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11)  NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_deposit_university_idx` (`university_id` ASC) )
DEFAULT COLLATE=utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `#__swa_ticket_type` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` INT(11)  NOT NULL ,
  `state` TINYINT(1)  NOT NULL ,
  `checked_out` INT(11)  NOT NULL ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11)  NOT NULL ,
  PRIMARY KEY (`id`) )
DEFAULT COLLATE=utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `#__swa_event` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) NOT NULL ,
  `university_id` INT NOT NULL ,
  `season_id` INT NOT NULL ,
  `capacity` INT NOT NULL ,
  `date_open` DATE NOT NULL ,
  `date_close` DATE NOT NULL ,
  `date` DATE NOT NULL ,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` INT(11)  NOT NULL ,
  `state` TINYINT(1)  NOT NULL ,
  `checked_out` INT(11)  NOT NULL ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11)  NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_event_university1_idx` (`university_id` ASC) ,
  INDEX `fk_event_season1_idx` (`season_id` ASC) )
DEFAULT COLLATE=utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `#__swa_event_ticket` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `event_id` INT NOT NULL ,
  `ticket_type_id` INT NOT NULL ,
  `quantity` INT NOT NULL ,
  `price` DECIMAL NOT NULL ,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` INT(11)  NOT NULL ,
  `state` TINYINT(1)  NOT NULL ,
  `checked_out` INT(11)  NOT NULL ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11)  NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_event_ticket_event1_idx` (`event_id` ASC) ,
  INDEX `fk_event_ticket_ticket_type1_idx` (`ticket_type_id` ASC) )
DEFAULT COLLATE=utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `#__swa_grant` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `event_id` VARCHAR(45) NOT NULL ,
  `application_date` DATE NOT NULL ,
  `amount` DECIMAL NOT NULL ,
  `fund_use` VARCHAR(255) NOT NULL ,
  `instructions` VARCHAR(255) NOT NULL ,
  `ac_sortcode` VARCHAR(8) NULL ,
  `ac_number` VARCHAR(8) NULL ,
  `ac_name` VARCHAR(200) NULL ,
  `finances_date` DATE NULL ,
  `finances_id` INT NULL ,
  `auth_date` DATE NULL ,
  `auth_id` INT NULL ,
  `payment_date` DATE NULL ,
  `payment_id` INT NULL ,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` INT(11)  NOT NULL ,
  `state` TINYINT(1)  NOT NULL ,
  `checked_out` INT(11)  NOT NULL ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11)  NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_grants_createdby1_idx` (`created_by` ASC) ,
  INDEX `fk_grants_event1_idx` (`event_id` ASC) )
DEFAULT COLLATE=utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `#__swa_ticket` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT NOT NULL ,
  `event_ticket_id` INT NOT NULL ,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` INT(11)  NOT NULL ,
  `state` TINYINT(1)  NOT NULL ,
  `checked_out` INT(11)  NOT NULL ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11)  NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_ticket_event_ticket1_idx` (`event_ticket_id` ASC) ,
  INDEX `fk_ticket_user1_idx` (`user_id` ASC) )
DEFAULT COLLATE=utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `#__swa_race_type` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` INT(11)  NOT NULL ,
  `state` TINYINT(1)  NOT NULL ,
  `checked_out` INT(11)  NOT NULL ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11)  NOT NULL ,
  PRIMARY KEY (`id`) )
DEFAULT COLLATE=utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `#__swa_race` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `event_id` INT NOT NULL ,
  `race_type_id` INT NOT NULL ,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` INT(11)  NOT NULL ,
  `state` TINYINT(1)  NOT NULL ,
  `checked_out` INT(11)  NOT NULL ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11)  NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_race_event1_idx` (`event_id` ASC) ,
  INDEX `fk_race_race_type1_idx` (`race_type_id` ASC) )
DEFAULT COLLATE=utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `#__swa_indi_result` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT NOT NULL ,
  `race_id` INT NOT NULL ,
  `result` INT NULL ,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` INT(11)  NOT NULL ,
  `state` TINYINT(1)  NOT NULL ,
  `checked_out` INT(11)  NOT NULL ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11)  NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_indi_result_race1_idx` (`race_id` ASC) ,
  INDEX `fk_indi_result_user1_idx` (`user_id` ASC) )
DEFAULT COLLATE=utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `#__swa_team_result` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `race_id` INT NOT NULL ,
  `university_id` INT NOT NULL ,
  `team_number` INT NOT NULL ,
  `result` INT NULL ,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` INT(11)  NOT NULL ,
  `state` TINYINT(1)  NOT NULL ,
  `checked_out` INT(11)  NOT NULL ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11)  NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_team_result_race1_idx` (`race_id` ASC) ,
  INDEX `fk_team_result_university1_idx` (`university_id` ASC) )
DEFAULT COLLATE=utf8_general_ci;

CREATE  TABLE IF NOT EXISTS `#__swa_damages` (
  `id` INT NOT NULL ,
  `event_id` INT NOT NULL ,
  `university_id` INT NOT NULL ,
  `date` DATE NOT NULL ,
  `cost` DECIMAL NOT NULL ,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `ordering` INT(11)  NOT NULL ,
  `state` TINYINT(1)  NOT NULL ,
  `checked_out` INT(11)  NOT NULL ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(11)  NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_damages_event1_idx` (`event_id` ASC) ,
  INDEX `fk_damages_university1_idx` (`university_id` ASC) )
DEFAULT COLLATE=utf8_general_ci;
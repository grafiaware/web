-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema events
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema events
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `events` DEFAULT CHARACTER SET utf8 ;
USE `events` ;

-- -----------------------------------------------------
-- Table `events`.`event_content_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`event_content_type` (
  `type` VARCHAR(45) NOT NULL,
  `name` VARCHAR(45) NULL DEFAULT '',
  PRIMARY KEY (`type`))
ENGINE = InnoDB
DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `events`.`institution_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`institution_type` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `institution_type` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `events`.`institution`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`institution` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NULL DEFAULT NULL,
  `institution_type_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_institution_institution_type1_idx` (`institution_type_id` ASC),
  CONSTRAINT `fk_institution_institution_type1`
    FOREIGN KEY (`institution_type_id`)
    REFERENCES `events`.`institution_type` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `events`.`event_content`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`event_content` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NULL DEFAULT NULL,
  `perex` VARCHAR(500) NULL DEFAULT NULL,
  `party` VARCHAR(200) NULL DEFAULT NULL,
  `event_content_type_type_fk` VARCHAR(45) NOT NULL,
  `institution_id_fk` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_event_content_event_content_type1_idx` (`event_content_type_type_fk` ASC),
  INDEX `fk_event_content_institution1_idx` (`institution_id_fk` ASC),
  CONSTRAINT `fk_event_content_event_content_type1`
    FOREIGN KEY (`event_content_type_type_fk`)
    REFERENCES `events`.`event_content_type` (`type`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_content_institution1`
    FOREIGN KEY (`institution_id_fk`)
    REFERENCES `events`.`institution` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `events`.`event_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`event_type` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `value` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `events`.`event`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`event` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `published` TINYINT(1) NULL DEFAULT NULL,
  `start` DATETIME NULL DEFAULT NULL,
  `end` DATETIME NULL DEFAULT NULL,
  `event_type_id_fk` INT(11) UNSIGNED NOT NULL,
  `event_content_id_fk` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_event_event_type1_idx` (`event_type_id_fk` ASC),
  INDEX `fk_event_event_content1_idx` (`event_content_id_fk` ASC),
  CONSTRAINT `fk_event_event_content1`
    FOREIGN KEY (`event_content_id_fk`)
    REFERENCES `events`.`event_content` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_event_event_type1`
    FOREIGN KEY (`event_type_id_fk`)
    REFERENCES `events`.`event_type` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `events`.`event_presentation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`event_presentation` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `show` TINYINT(1) NULL DEFAULT NULL,
  `platform` VARCHAR(45) NULL DEFAULT NULL,
  `url` VARCHAR(200) NULL DEFAULT NULL,
  `event_id_fk` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_event_presentation_event1_idx` (`event_id_fk` ASC),
  CONSTRAINT `fk_event_presentation_event1`
    FOREIGN KEY (`event_id_fk`)
    REFERENCES `events`.`event` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `events`.`login`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`login` (
  `login_name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`login_name`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `events`.`presenter`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`presenter` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `login_name_fk` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_presenter_login1_idx` (`login_name_fk` ASC),
  CONSTRAINT `fk_presenter_login1`
    FOREIGN KEY (`login_name_fk`)
    REFERENCES `events`.`login` (`login_name`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `events`.`presenter_data`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`presenter_data` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(90) NULL DEFAULT NULL,
  `short_name` VARCHAR(45) NULL DEFAULT NULL,
  `presenter_id_fk` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_presenter_data_presenter1_idx` (`presenter_id_fk` ASC),
  CONSTRAINT `fk_presenter_data_presenter1`
    FOREIGN KEY (`presenter_id_fk`)
    REFERENCES `events`.`presenter` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `events`.`presenter_party`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`presenter_party` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `prefix` VARCHAR(45) NULL DEFAULT NULL,
  `member_name` VARCHAR(90) NULL DEFAULT NULL,
  `member_surname` VARCHAR(90) NULL DEFAULT NULL,
  `postfix` VARCHAR(45) NULL DEFAULT NULL,
  `email` VARCHAR(90) NULL DEFAULT NULL,
  `phone` VARCHAR(45) NULL DEFAULT NULL,
  `presenter_id_fk` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_presenter_party_presenter1_idx` (`presenter_id_fk` ASC),
  CONSTRAINT `fk_presenter_party_presenter1`
    FOREIGN KEY (`presenter_id_fk`)
    REFERENCES `events`.`presenter` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `events`.`presenter_to_event`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`presenter_to_event` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `presenter_id` INT(11) UNSIGNED NOT NULL,
  `event_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_presenter_to_event_presenter1_idx` (`presenter_id` ASC),
  INDEX `fk_presenter_to_event_event1_idx` (`event_id` ASC),
  CONSTRAINT `fk_presenter_to_event_event1`
    FOREIGN KEY (`event_id`)
    REFERENCES `events`.`event` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_presenter_to_event_presenter1`
    FOREIGN KEY (`presenter_id`)
    REFERENCES `events`.`presenter` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `events`.`visitor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`visitor` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `login_login_name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_visitor_login1_idx` (`login_login_name` ASC),
  CONSTRAINT `fk_visitor_login1`
    FOREIGN KEY (`login_login_name`)
    REFERENCES `events`.`login` (`login_name`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `events`.`visitior_data`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`visitior_data` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `prefix` VARCHAR(45) NULL DEFAULT NULL,
  `name` VARCHAR(90) NULL DEFAULT NULL,
  `surname` VARCHAR(90) NULL DEFAULT NULL,
  `postfix` VARCHAR(45) NULL DEFAULT NULL,
  `email` VARCHAR(90) NULL DEFAULT NULL,
  `phone` VARCHAR(45) NULL DEFAULT NULL,
  `cv_education_text` VARCHAR(3000) NULL DEFAULT NULL,
  `cv_skills_text` VARCHAR(3000) NULL DEFAULT NULL,
  `cv_document` MEDIUMBLOB NULL DEFAULT NULL,
  `letter_document` MEDIUMBLOB NULL DEFAULT NULL,
  `visitor_id_fk` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_data_package_visitor1_idx` (`visitor_id_fk` ASC),
  CONSTRAINT `fk_data_package_visitor1`
    FOREIGN KEY (`visitor_id_fk`)
    REFERENCES `events`.`visitor` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `events`.`visitor_to_event`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `events`.`visitor_to_event` (
  `id_visitor_to_event` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `visitor_id_fk` INT(11) UNSIGNED NOT NULL,
  `created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `event_id_fk` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_visitor_to_event`),
  INDEX `fk_visitor_to_event_visitor1_idx` (`visitor_id_fk` ASC),
  INDEX `fk_visitor_to_event_event1_idx` (`event_id_fk` ASC),
  CONSTRAINT `fk_visitor_to_event_event1`
    FOREIGN KEY (`event_id_fk`)
    REFERENCES `events`.`event` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_visitor_to_event_visitor1`
    FOREIGN KEY (`visitor_id_fk`)
    REFERENCES `events`.`visitor` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

CREATE DATABASE accounts;

CREATE TABLE `accounts`.`users`
(
    `id` INT NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `hash` VARCHAR(255) NOT NULL,
    `active` BOOL NOT NULL DEFAULT 0,
PRIMARY KEY (`id`)
);

CREATE TABLE `accounts`.`failed_logins`
(
    `id` INT NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(100) NOT NULL,
    `theCount` INT NOT NULL DEFAULT 1,
    `last_time` INT NOT NULL,
 PRIMARY KEY (`id`)
 );

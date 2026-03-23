-- Create Database
CREATE DATABASE IF NOT EXISTS VotingSystem;
USE VotingSystem;

-- Roles Table
CREATE TABLE `Roles` (
  `role_id` INT NOT NULL AUTO_INCREMENT, 
  `role_name` VARCHAR(255) NOT NULL, 
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1000;

-- Users Table 
CREATE TABLE `Users` (
    `user_id` INT NOT NULL AUTO_INCREMENT,
    `student_id` VARCHAR(20) NOT NULL UNIQUE,
    `username` VARCHAR(255) NOT NULL UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role_id` INT NOT NULL,
    `created_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    FOREIGN KEY (`role_id`) REFERENCES `Roles`(`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1000;

-- Insert Roles
INSERT INTO `Roles` (`role_name`) VALUES ('admin');
INSERT INTO `Roles` (`role_name`) VALUES ('student');

-- Insert Sample User
INSERT INTO `Users`
(`student_id`, `username`, `email`, `password`, `role_id`) 
VALUES 
('20240000001', 'tephL', 'tephl@gmail.com', '123456', 1000);
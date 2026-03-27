DROP DATABASE VotingSystem;
CREATE DATABASE VotingSystem;
USE VotingSystem;

CREATE TABLE `VotingSystem`.`Roles` (
  `role_id` INT NOT NULL AUTO_INCREMENT, 
  `role_name` VARCHAR(255) NOT NULL, 
  PRIMARY KEY (`role_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Users` (
    `user_id` INT NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role_id` INT NOT NULL,
    `activated_status` TINYINT(1) NOT NULL DEFAULT 0,
    `created_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    FOREIGN KEY (`role_id`) REFERENCES `Roles`(`role_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`College` (
    `college_id` INT NOT NULL AUTO_INCREMENT,
    `college_name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`college_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Students` (
    `student_id` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(255) NOT NULL,
    `middle_name` VARCHAR(255),
    `last_name` VARCHAR(255) NOT NULL,
    `college_id` INT NOT NULL,
    PRIMARY KEY (`student_id`),
    FOREIGN KEY (`college_id`) REFERENCES `College`(`college_id`)
) ENGINE = InnoDB;

CREATE TABLE `VotingSystem`.`StudentVoters` (
    `studentvoter_id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `student_id` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`studentvoter_id`),
    FOREIGN KEY (`user_id`) REFERENCES `Users`(`user_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;


-- Roles
INSERT INTO Roles(role_name) VALUES ("admin");
INSERT INTO Roles(role_name) VALUES ('student_voter');
-- Sample Users

-- college
INSERT INTO College(college_name) VALUES
('College of Engineering'),
('College of Business'),
('College of Information and Communications Technology'),
('College of Computer Studies'),
('College of Arts and Sciences');
-- student 
INSERT INTO Students(student_id, first_name, middle_name, last_name, college_id) VALUES
('1000000001', 'Stephen', 'Alex', 'Lopez', 1000),
('1000000002', 'Sandra', 'Batongbakal', 'Reyes', 1002),
('1000000003', 'Ian', 'Cato', 'Castro', 1003),
('1000000004', 'Justine', 'Dimasilangan', 'Garcia', 1003),
('1000000005', 'Mark', 'Enteng', 'Santos', 1001),
('1000000006', 'John', 'Festival', 'Cruz', 1004);
-- admin
INSERT INTO Users(username, email, password, role_id, activated_status) VALUES ('tephL','tephL@example.com', '123456', 1000, 1);
-- user
INSERT INTO Users(username, email, password, role_id, activated_status) VALUES
('Stephen', 'stephen@example.com', '676767', 1001, 1),
('Sandra', 'sandra@example.com', '676767', 1001, 1),
('IannCat', 'ianncat@example.com', '676767', 1001, 1),
('Justine', 'justine@example.com', '676767', 1001, 1),
('MarkJoseph', 'mark@example.com', '676767', 1001, 1),
('JohnPaul', 'john@example.com', '676767', 1001, 1);







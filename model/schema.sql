CREATE DATABASE VotingSystem;
USE DATABASE VotingSystem;

CREATE TABLE `VotingSystem`.`Roles` (
  `role_id` INT NOT NULL AUTO_INCREMENT, 
  `role_name` VARCHAR(255) NOT NULL, 
  PRIMARY KEY (`role_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;


CREATE TABLE `VotingSystem`.`Users` (
    `user_id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role_id` INT NOT NULL,
    `activated_status` TINYINT(1) NOT NULL DEFAULT 0,
    `created_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    FOREIGN KEY (`role_id`) REFERENCES `Roles`(`role_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;


-- Roles
INSERT INTO Roles(role_name) VALUES ("admin");
INSERT INTO Roles(role_name) VALUES ('student_voter');


-- Sample Users
-- admin
INSERT INTO Users(username, password, role_id, activated_status) VALUES ('tephL', '123456', 1000, 1);
-- student_voter
INSERT INTO Users(username, password, role_id) VALUES 
('Stephen', '676767', 1001),
('Sandra', '676767', 1001),
('IannCat', '676767', 1001),
('Justine', '676767', 1001),
('MarkJoseph', '676767', 1001),
('JohnPaul', '676767', 1001),
;

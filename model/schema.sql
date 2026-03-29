DROP DATABASE IF EXISTS VotingSystem;
CREATE DATABASE VotingSystem;
USE VotingSystem;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- EXTERNAL INFO
-- ============================================================

CREATE TABLE `VotingSystem`.`College` (
    `college_id`   INT          NOT NULL AUTO_INCREMENT,
    `college_name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`college_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Students` (
    `student_id`  INT          NOT NULL AUTO_INCREMENT,
    `first_name`  VARCHAR(255) NOT NULL,
    `middle_name` VARCHAR(255),
    `last_name`   VARCHAR(255) NOT NULL,
    `college_id`  INT          NOT NULL,
    PRIMARY KEY (`student_id`),
    FOREIGN KEY (`college_id`) REFERENCES `College`(`college_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

-- ============================================================
-- ACCOUNTS
-- ============================================================

CREATE TABLE `VotingSystem`.`Roles` (
    `role_id`   INT          NOT NULL AUTO_INCREMENT,
    `role_name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`role_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Users` (
    `user_id`          INT          NOT NULL AUTO_INCREMENT,
    `username`         VARCHAR(255) NOT NULL,
    `email`            VARCHAR(255) NOT NULL,
    `password`         VARCHAR(255) NOT NULL,
    `role_id`          INT          NOT NULL,
    `activated_status` TINYINT(1)   NOT NULL DEFAULT 0,
    `created_date`     DATETIME              DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`),
    FOREIGN KEY (`role_id`) REFERENCES `Roles`(`role_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`StudentVoters` (
    `studentvoter_id` INT        NOT NULL AUTO_INCREMENT,
    `user_id`         INT        NOT NULL,
    `student_id`      INT        NOT NULL,
    `has_voted`       TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`studentvoter_id`),
    FOREIGN KEY (`user_id`)    REFERENCES `Users`(`user_id`),
    FOREIGN KEY (`student_id`) REFERENCES `Students`(`student_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Admins` (
    `admin_id`       INT          NOT NULL AUTO_INCREMENT,
    `first_name`     VARCHAR(255) NOT NULL,
    `middle_name`    VARCHAR(255),
    `last_name`      VARCHAR(255) NOT NULL,
    `contact_number` VARCHAR(20),
    `user_id`        INT          NOT NULL,
    PRIMARY KEY (`admin_id`),
    FOREIGN KEY (`user_id`) REFERENCES `Users`(`user_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;


-- ============================================================
-- VOTING PROCESS
-- ============================================================

CREATE TABLE `VotingSystem`.`Elections` (
    `election_id`    INT          NOT NULL AUTO_INCREMENT,
    `election_title` VARCHAR(255) NOT NULL,
    `status`         VARCHAR(50)  NOT NULL,
    `start_date`     DATETIME     NOT NULL,
    `end_date`       DATETIME     NOT NULL,
    PRIMARY KEY (`election_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Positions` (
    `position_id`   INT          NOT NULL AUTO_INCREMENT,
    `position_name` VARCHAR(255) NOT NULL,
    `election_id`   INT          NOT NULL,
    PRIMARY KEY (`position_id`),
    FOREIGN KEY (`election_id`) REFERENCES `Elections`(`election_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Partylists` (
    `partylist_id`   INT          NOT NULL AUTO_INCREMENT,
    `partylist_name` VARCHAR(255) NOT NULL,
    `election_id`    INT          NOT NULL,
    PRIMARY KEY (`partylist_id`),
    FOREIGN KEY (`election_id`) REFERENCES `Elections`(`election_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Candidates` (
    `candidate_id` INT NOT NULL AUTO_INCREMENT,
    `partylist_id` INT NOT NULL,
    `student_id`   INT NOT NULL,
    `position_id`  INT NOT NULL,
    PRIMARY KEY (`candidate_id`),
    FOREIGN KEY (`partylist_id`) REFERENCES `Partylists`(`partylist_id`),
    FOREIGN KEY (`student_id`)   REFERENCES `Students`(`student_id`),
    FOREIGN KEY (`position_id`)  REFERENCES `Positions`(`position_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Votes` (
    `vote_id`         INT      NOT NULL AUTO_INCREMENT,
    `vote_date`       DATETIME          DEFAULT CURRENT_TIMESTAMP,
    `studentvoter_id` INT      NOT NULL,
    `candidate_id`    INT      NULL,
    `position_id`     INT      NOT NULL,
    PRIMARY KEY (`vote_id`),
    UNIQUE (`studentvoter_id`, `position_id`, `candidate_id`),
    FOREIGN KEY (`studentvoter_id`) REFERENCES `StudentVoters`(`studentvoter_id`),
    FOREIGN KEY (`candidate_id`)    REFERENCES `Candidates`(`candidate_id`),
    FOREIGN KEY (`position_id`)     REFERENCES `Positions`(`position_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1;


SET FOREIGN_KEY_CHECKS = 1;


-- ============================================================
-- EXTERNAL INFO : DATA
-- ============================================================

-- Colleges (college_id: 1000–1004)
INSERT INTO College (college_name) VALUES
('College of Engineering'),                              -- college_id: 1000
('College of Information Technology'),                   -- college_id: 1001
('College of Arts and Letters'),                         -- college_id: 1002
('College of Social Sciences and Philosophy'),           -- college_id: 1003
('College of Education');                                -- college_id: 1004

-- Students (student_id: 1000–1015)
INSERT INTO Students (first_name, middle_name, last_name, college_id) VALUES
('Juan',      'Santos',     'Dela Cruz', 1000),          -- student_id: 1000
('Maria',     'Reyes',      'Garcia',    1001),          -- student_id: 1001
('Carlos',    'Mendoza',    'Lopez',     1002),          -- student_id: 1002
('Ana',       'Cruz',       'Martinez',  1003),          -- student_id: 1003
('Jose',      'Bautista',   'Rodriguez', 1004),          -- student_id: 1004
('Luisa',     'Villanueva', 'Hernandez', 1000),          -- student_id: 1005
('Miguel',    'Aquino',     'Gonzales',  1001),          -- student_id: 1006
('Sofia',     'Ramos',      'Perez',     1002),          -- student_id: 1007
('Ramon',     'Torres',     'Castillo',  1003),          -- student_id: 1008
('Elena',     'Flores',     'Morales',   1004),          -- student_id: 1009
('Diego',     'Pascual',    'Navarro',   1000),          -- student_id: 1010
('Isabella',  'Aguilar',    'Reyes',     1001),          -- student_id: 1011
('Marco',     'Domingo',    'Santiago',  1002),          -- student_id: 1012
('Gabrielle', 'Espinosa',   'Valdez',    1003),          -- student_id: 1013
('Rafael',    'Mercado',    'Salazar',   1004),          -- student_id: 1014
('Camille',   'Ocampo',     'Fuentes',   1000);          -- student_id: 1015

-- ============================================================
-- ACCOUNTS 
-- ============================================================

CREATE TABLE `VotingSystem`.`Roles` (
`role_id`   INT          NOT NULL AUTO_INCREMENT,
`role_name` VARCHAR(255) NOT NULL,
PRIMARY KEY (`role_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Users` (
`user_id`          INT          NOT NULL AUTO_INCREMENT,
`username`         VARCHAR(255) NOT NULL,
`email`            VARCHAR(255) NOT NULL,
`password`         VARCHAR(255) NOT NULL,
`role_id`          INT          NOT NULL,
`activated_status` TINYINT(1)   NOT NULL DEFAULT 0,
`created_date`     DATETIME              DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`user_id`),
FOREIGN KEY (`role_id`) REFERENCES `Roles`(`role_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Admins` (
`admin_id`       INT          NOT NULL AUTO_INCREMENT,
`first_name`     VARCHAR(255) NOT NULL,
`middle_name`    VARCHAR(255),
`last_name`      VARCHAR(255) NOT NULL,
`contact_number` VARCHAR(20),
`user_id`        INT          NOT NULL,
PRIMARY KEY (`admin_id`),
FOREIGN KEY (`user_id`) REFERENCES `Users`(`user_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`StudentVoters` (
`studentvoter_id` INT        NOT NULL AUTO_INCREMENT,
`user_id`         INT        NOT NULL,
`student_id`      INT        NOT NULL,
`has_voted`       TINYINT(1) NOT NULL DEFAULT 0,
PRIMARY KEY (`studentvoter_id`),
FOREIGN KEY (`user_id`)    REFERENCES `Users`(`user_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;


-- ============================================================
-- ACCOUNTS : DATA
-- ============================================================

-- Roles (role_id: 1000 = admin, 1001 = student_voter)
INSERT INTO Roles (role_name) VALUES ('admin');          -- role_id: 1000
INSERT INTO Roles (role_name) VALUES ('student_voter');  -- role_id: 1001

-- Users: Admin (user_id: 1000)
INSERT INTO Users (username, email, password, role_id, activated_status) VALUES
('tephL', 'tephL@example.com', '1234567878', 1000, 1);      -- user_id: 1000

-- Users: Student Voters (user_id: 1001–1016)
INSERT INTO Users (username, email, password, role_id) VALUES
('juan.delacruz',    'juan@example.com',       '12345678', 1001),  -- user_id: 1001
('maria.garcia',     'maria@example.com',      '12345678', 1001),  -- user_id: 1002
('carlos.lopez',     'carlos@example.com',     '12345678', 1001),  -- user_id: 1003
('ana.martinez',     'ana@example.com',        '12345678', 1001),  -- user_id: 1004
('jose.rodriguez',   'jose@example.com',       '12345678', 1001),  -- user_id: 1005
('luisa.hernandez',  'luisa@example.com',      '12345678', 1001),  -- user_id: 1006
('miguel.gonzales',  'miguel@example.com',     '12345678', 1001),  -- user_id: 1007
('sofia.perez',      'sofia@example.com',      '12345678', 1001),  -- user_id: 1008
('ramon.castillo',   'ramon@example.com',      '12345678', 1001),  -- user_id: 1009
('elena.morales',    'elena@example.com',      '12345678', 1001),  -- user_id: 1010
('diego.navarro',    'diego@example.com',      '12345678', 1001),  -- user_id: 1011
('isabella.reyes',   'isabella@example.com',   '12345678', 1001),  -- user_id: 1012
('marco.santiago',   'marco@example.com',      '12345678', 1001),  -- user_id: 1013
('gabrielle.valdez', 'gabrielle@example.com',  '12345678', 1001),  -- user_id: 1014
('rafael.salazar',   'rafael@example.com',     '12345678', 1001),  -- user_id: 1015
('camille.fuentes',  'camille@example.com',    '12345678', 1001);  -- user_id: 1016

-- Admins (admin_id: 1000)
INSERT INTO Admins (first_name, middle_name, last_name, contact_number, user_id) VALUES
('Steph', NULL, 'L', '09000000000', 1000);              -- admin_id: 1000

-- StudentVoters (studentvoter_id: 1000–1015)
INSERT INTO StudentVoters (user_id, student_id) VALUES
(1001, 1000),                                            -- studentvoter_id: 1000 | Juan Dela Cruz
(1002, 1001),                                            -- studentvoter_id: 1001 | Maria Garcia
(1003, 1002),                                            -- studentvoter_id: 1002 | Carlos Lopez
(1004, 1003),                                            -- studentvoter_id: 1003 | Ana Martinez
(1005, 1004),                                            -- studentvoter_id: 1004 | Jose Rodriguez
(1006, 1005),                                            -- studentvoter_id: 1005 | Luisa Hernandez
(1007, 1006),                                            -- studentvoter_id: 1006 | Miguel Gonzales
(1008, 1007),                                            -- studentvoter_id: 1007 | Sofia Perez
(1009, 1008),                                            -- studentvoter_id: 1008 | Ramon Castillo
(1010, 1009),                                            -- studentvoter_id: 1009 | Elena Morales
(1011, 1010),                                            -- studentvoter_id: 1010 | Diego Navarro
(1012, 1011),                                            -- studentvoter_id: 1011 | Isabella Reyes
(1013, 1012),                                            -- studentvoter_id: 1012 | Marco Santiago
(1014, 1013),                                            -- studentvoter_id: 1013 | Gabrielle Valdez
(1015, 1014),                                            -- studentvoter_id: 1014 | Rafael Salazar
(1016, 1015);                                            -- studentvoter_id: 1015 | Camille Fuentes

-- ============================================================
-- VOTING PROCESS 
-- ============================================================

CREATE TABLE `VotingSystem`.`Elections` (
`election_id`    INT          NOT NULL AUTO_INCREMENT,
`election_title` VARCHAR(255) NOT NULL,
`status`         VARCHAR(50)  NOT NULL,
`start_date`     DATETIME     NOT NULL,
`end_date`       DATETIME     NOT NULL,
PRIMARY KEY (`election_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Positions` (
`position_id`   INT          NOT NULL AUTO_INCREMENT,
`position_name` VARCHAR(255) NOT NULL,
`election_id`   INT          NOT NULL,
PRIMARY KEY (`position_id`),
FOREIGN KEY (`election_id`) REFERENCES `Elections`(`election_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Partylists` (
`partylist_id`   INT          NOT NULL AUTO_INCREMENT,
`partylist_name` VARCHAR(255) NOT NULL,
`election_id`    INT          NOT NULL,
PRIMARY KEY (`partylist_id`),
FOREIGN KEY (`election_id`) REFERENCES `Elections`(`election_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Candidates` (
`candidate_id` INT NOT NULL AUTO_INCREMENT,
`partylist_id` INT NOT NULL,
`student_id`   INT NOT NULL,
`position_id`  INT NOT NULL,
PRIMARY KEY (`candidate_id`),
FOREIGN KEY (`partylist_id`) REFERENCES `Partylists`(`partylist_id`),
FOREIGN KEY (`student_id`)   REFERENCES `Students`(`student_id`),
FOREIGN KEY (`position_id`)  REFERENCES `Positions`(`position_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Votes` (
`vote_id`         INT      NOT NULL AUTO_INCREMENT,
`vote_date`       DATETIME          DEFAULT CURRENT_TIMESTAMP,
`studentvoter_id` INT      NOT NULL,
`candidate_id`    INT      NOT NULL,
`position_id`     INT      NOT NULL,
PRIMARY KEY (`vote_id`),
UNIQUE (`studentvoter_id`, `position_id`, `candidate_id`),
FOREIGN KEY (`studentvoter_id`) REFERENCES `StudentVoters`(`studentvoter_id`),
FOREIGN KEY (`candidate_id`)    REFERENCES `Candidates`(`candidate_id`),
FOREIGN KEY (`position_id`)     REFERENCES `Positions`(`position_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1;


SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- VOTING PROCESS: DATA
-- ============================================================

-- Elections (election_id: 1000)
INSERT INTO Elections (election_title, status, start_date, end_date) VALUES
('Student Council Election 2025', 'active', '2025-06-01 08:00:00', '2025-06-01 17:00:00');
-- election_id: 1000

-- Positions (position_id: 1000–1003)
INSERT INTO Positions (position_name, election_id) VALUES
('President',      1000),                               -- position_id: 1000
('Vice-President', 1000),                               -- position_id: 1001
('Senator',        1000),                               -- position_id: 1002
('Vice-Governor',  1000);                               -- position_id: 1003

-- Partylists (partylist_id: 1000–1001)
INSERT INTO Partylists (partylist_name, election_id) VALUES
('Partido Uno', 1000),                                  -- partylist_id: 1000
('Partido Dos', 1000);                                  -- partylist_id: 1001

-- Candidates (candidate_id: 1000–1013)
INSERT INTO Candidates (partylist_id, student_id, position_id) VALUES
-- Partido Uno (partylist_id: 1000)
(1000, 2024000000, 1000),                               -- candidate_id: 1000 | Juan Dela Cruz   -> President
(1000, 2024000001, 1001),                               -- candidate_id: 1001 | Maria Garcia     -> Vice-President
(1000, 2024000002, 1002),                               -- candidate_id: 1002 | Carlos Lopez     -> Senator
(1000, 2024000003, 1002),                               -- candidate_id: 1003 | Ana Martinez     -> Senator
(1000, 2024000004, 1002),                               -- candidate_id: 1004 | Jose Rodriguez   -> Senator
(1000, 2024000005, 1003),                               -- candidate_id: 1005 | Luisa Hernandez  -> Vice-Governor
(1000, 2024000006, 1002),                               -- candidate_id: 1006 | Miguel Gonzales  -> Senator
-- Partido Dos (partylist_id: 1001)
(1001, 2024000008, 1000),                               -- candidate_id: 1007 | Ramon Castillo   -> President
(1001, 2024000009, 1001),                               -- candidate_id: 1008 | Elena Morales    -> Vice-President
(1001, 2024000010, 1002),                               -- candidate_id: 1009 | Diego Navarro    -> Senator
(1001, 2024000011, 1002),                               -- candidate_id: 1010 | Isabella Reyes   -> Senator
(1001, 2024000012, 1002),                               -- candidate_id: 1011 | Marco Santiago   -> Senator
(1001, 2024000013, 1003),                               -- candidate_id: 1012 | Gabrielle Valdez -> Vice-Governor
(1001, 2024000015, 1002);                               -- candidate_id: 1013 | Camille Fuentes  -> Senator 

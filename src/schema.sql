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
`student_id`  BIGINT          NOT NULL AUTO_INCREMENT,
`first_name`  VARCHAR(255) NOT NULL,
`middle_name` VARCHAR(255),
`last_name`   VARCHAR(255) NOT NULL,
`college_id`  INT          NOT NULL,
PRIMARY KEY (`student_id`),
FOREIGN KEY (`college_id`) REFERENCES `College`(`college_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 2026000000;

-- ============================================================
-- EXTERNAL INFO : DATA
-- ============================================================

-- Colleges (college_id: 1000–1004)
INSERT INTO College (college_name) VALUES
('College of Information Technology'),                              -- college_id: 1000
('College of Engineering'),                   -- college_id: 1001
('College of Arts and Letters'),                         -- college_id: 1002
('College of Social Sciences and Philosophy'),           -- college_id: 1003
('College of Education');                                -- college_id: 1004

-- Students (student_id: 1000–1015)
INSERT INTO Students (student_id, first_name, middle_name, last_name, college_id) VALUES
('2025100001', 'Jonathan',      'Santos',     'Tomacruz', 1000),          
('2025100002', 'Ramon',     'Reyes',      'Castillo',    1001),          
('2025100003', 'Carlos',    'Mendoza',    'Lopez',     1002),          
('2025100004', 'Ana',       'Cruz',       'Martinez',  1003),          
('2025100005', 'Jose',      'Bautista',   'Rodriguez', 1004),          
('2025100006', 'Luisa',     'Villanueva', 'Hernandez', 1000),          
('2025100007', 'Miguel',    'Aquino',     'Gonzales',  1001),          
('2025100008', 'Sofia',     'Ramos',      'Perez',     1002),          
('2025100009', 'Robethel',     'Torres',     'Reyes',  1000),          
('2025100010', 'Elena',     'Flores',     'Morales',   1004),          
('2025100011', 'Diego',     'Pascual',    'Navarro',   1000),          
('2025100012', 'Isabella',  'Aguilar',    'Reyes',     1001),          
('2025100013', 'Marco',     'Domingo',    'Santiago',  1002),          
('2025100014', 'Gabrielle', 'Espinosa',   'Valdez',    1003),          
('2025100015', 'Rafael',    'Mercado',    'Salazar',   1004),          
('2025100016', 'Camille',   'Ocampo',     'Fuentes',   1000),
-- start for dummy studentvoters               
('2025100017', 'John',   'Bruh',     'Doe',   1000), 
('2025100018', 'Jane', 'Bruh',   'Smith',    1003),          
('2025100019', 'Mike',    'Bruh',    'Ross',   1004),          
('2025100020', 'Rachel',   'Bruh',     'Zane',   1000),          
('2025100021', 'Harvey',   'Ocampo',     'Specter',   1000); 


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
`student_id`      BIGINT        NOT NULL,
PRIMARY KEY (`studentvoter_id`),
FOREIGN KEY (`user_id`)    REFERENCES `Users`(`user_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;


-- ============================================================
-- ACCOUNTS : DATA
-- ============================================================

-- Roles (role_id: 1000 = admin, 1001 = student_voter)
INSERT INTO Roles (role_id, role_name) VALUES 
('3000', 'master_admin'),  -- role_id: 3000
('3001', 'election_admin'),  -- role_id: 3001
('3002', 'voters_admin'),  -- role_id: 3002
('1000', 'student_voter');  -- role_id: 1000

-- Users: Admin (user_id: 1000)
INSERT INTO Users (username, email, password, role_id, activated_status) VALUES
('tephL', 'tephL@example.com', '1234567878', 3000, 1), 
('justine', 'justine@example.com', '1234567878', 3001, 1), 
('sandrara', 'sandra@example.com', '1234567878', 3001, 1),
('luigicat', 'luigi@example.com', '1234567878', 3002, 1),
('johnpaul', 'jp@example.com', '1234567878', 3002, 1),
('markjoseph', 'mj@example.com', '1234567878', 3002, 1); 

-- Users: Student Voters (user_id: 1001–1016)
INSERT INTO Users (username, email, password, role_id, activated_status) VALUES
('john_doe', 'john.doe@example.com', '321321321', 1000, 1), -- 1006
('jane_smith', 'jane.smith@example.com', '321321321', 1000, 1), -- 1007
('mike_ross', 'mike.ross@example.com', '321321321', 1000, 1), -- 1008
('rachel_zane', 'rachel.zane@example.com', '321321321', 1000, 1), -- 1009
('harvey_specter', 'harvey.specter@example.com', '321321321', 1000, 1); -- 1010

-- Admins (admin_id: 1000)
INSERT INTO Admins (first_name, middle_name, last_name, contact_number, user_id) VALUES
('Stephen', 'Lee', 'Astrera', '09000000000', 1000),
('Justine', NULL, 'Dimla', '09000000000', 1001),
('Sandra', NULL, 'Cabasal', '09000000000', 1002),
('Luigi', NULL, 'Cato', '09000000000', 1003),
('John Paul', NULL, 'Sarmiento', '09000000000', 1004),
('Mark Joseph', NULL, 'Galino', '09000000000', 1005);      

-- StudentVoters (studentvoter_id: 1006–1010)
INSERT INTO StudentVoters (user_id, student_id) VALUES
(1006, 2025100017), -- 1000
(1007, 2025100018), -- 1001
(1008, 2025100019), -- 1002
(1009, 2025100020), -- 1003
(1010, 2025100021); -- 1004


-- ============================================================
-- VOTING PROCESS 
-- ============================================================

CREATE TABLE `VotingSystem`.`Elections` (
`election_id`    INT          NOT NULL AUTO_INCREMENT,
`election_title` VARCHAR(255) NOT NULL,
`status` ENUM('upcoming', 'active', 'completed') NOT NULL DEFAULT 'upcoming',
`start_date`     DATETIME     NOT NULL,
`end_date`       DATETIME     NOT NULL,
PRIMARY KEY (`election_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Positions` (
`position_id`   INT          NOT NULL AUTO_INCREMENT,
`position_name` VARCHAR(255) NOT NULL,
`election_id`   INT          NOT NULL,
`max_votes` INT NOT NULL,
PRIMARY KEY (`position_id`),
FOREIGN KEY (`election_id`) REFERENCES `Elections`(`election_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`PoliticalParties` (
  `party_id`      INT          NOT NULL AUTO_INCREMENT,
  `party_name`    VARCHAR(255) NOT NULL,
  `status`        ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `creation_date` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `election_id`   INT          NOT NULL,
  PRIMARY KEY (`party_id`),
  FOREIGN KEY (`election_id`) REFERENCES `Elections`(`election_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Candidates` (
  `candidate_id` INT NOT NULL AUTO_INCREMENT,
  `party_id`     INT NOT NULL,
  `student_id`   BIGINT NOT NULL,
  `election_id`  INT NOT NULL,
  `position_id`  INT NOT NULL,
  PRIMARY KEY (`candidate_id`),
  FOREIGN KEY (`party_id`)    REFERENCES `PoliticalParties`(`party_id`),
  FOREIGN KEY (`student_id`)  REFERENCES `Students`(`student_id`),
  FOREIGN KEY (`election_id`) REFERENCES `Elections`(`election_id`),
  FOREIGN KEY (`position_id`) REFERENCES `Positions`(`position_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1000;

CREATE TABLE `VotingSystem`.`Votes` (
  `vote_id`         INT      NOT NULL AUTO_INCREMENT,
  `vote_date`       DATETIME          DEFAULT CURRENT_TIMESTAMP,
  `studentvoter_id` INT      NOT NULL,
  `candidate_id`    INT      NULL,
  `position_id`     INT      NOT NULL,
  `election_id`     INT      NOT NULL,
  PRIMARY KEY (`vote_id`),
  UNIQUE (`studentvoter_id`, `position_id`, `candidate_id`, `election_id`),
  FOREIGN KEY (`studentvoter_id`) REFERENCES `StudentVoters`(`studentvoter_id`),
  FOREIGN KEY (`candidate_id`)    REFERENCES `Candidates`(`candidate_id`),
  FOREIGN KEY (`position_id`)     REFERENCES `Positions`(`position_id`),
  FOREIGN KEY (`election_id`)     REFERENCES `Elections`(`election_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- VOTING PROCESS: DATA
-- ============================================================

-- Sample Election (1000)
INSERT INTO Elections (election_title, status, start_date, end_date) VALUES
('Student Council Election 2025', 'completed', '2025-06-01 08:00:00', '2025-06-15 17:00:00'); 

-- Sample Positions for Election 1000
INSERT INTO Positions (position_name, max_votes, election_id) VALUES
('President', 1,      1000), -- 1000
('Vice-President', 1, 1000), -- 1001
('Senator', 4,       1000), -- 1002
('Vice-Governor', 1,  1000); -- 1003

-- Sample Parties for Election 1000
INSERT INTO PoliticalParties (party_name, election_id) VALUES
('Partido Uno', 1000), -- 1000
('Partido Dos', 1000); -- 1001

-- Sample Candidates for Election 1000
INSERT INTO Candidates (party_id, student_id, election_id, position_id) VALUES
-- Patido Uno
(1000, 2025100001, 1000, 1000), -- President
(1000, 2025100002, 1000, 1001), -- Vice President
(1000, 2025100003, 1000, 1002), -- Senators
(1000, 2025100004, 1000, 1002), -- Senators
(1000, 2025100005, 1000, 1002), -- Senators
(1000, 2025100006, 1000, 1002), -- Senators
(1000, 2025100007, 1000, 1003), -- Vice Governor
-- Partido Dos
(1001, 2025100009, 1000, 1000), -- President
(1001, 2025100008, 1000, 1001), -- Vice President
(1001, 2025100010, 1000, 1002), -- Senators
(1001, 2025100011, 1000, 1002), -- Senators
(1001, 2025100012, 1000, 1002), -- Senators
(1001, 2025100013, 1000, 1002), -- Senators
(1001, 2025100014, 1000, 1003);  -- Vice Governor

-- Sample Dummy Votes
INSERT INTO Votes(vote_date, studentvoter_id, candidate_id, position_id, election_id) VALUES
-- john doe votes (Uno Suporter)
('2025-06-01 14:00:00', 1000, 1000, 1000, 1000),
('2025-06-01 14:00:00', 1000, 1001, 1001, 1000),
('2025-06-01 14:00:00', 1000, 1002, 1002, 1000),
('2025-06-01 14:00:00', 1000, 1003, 1002, 1000),
('2025-06-01 14:00:00', 1000, 1004, 1002, 1000),
('2025-06-01 14:00:00', 1000, 1005, 1002, 1000),
('2025-06-01 14:00:00', 1000, 1006, 1003, 1000),
-- jane votes (Dos Supporter)
('2025-06-01 14:00:00', 1001, 1007, 1000, 1000),
('2025-06-01 14:00:00', 1001, 1008, 1001, 1000),
('2025-06-01 14:00:00', 1001, 1009, 1002, 1000),
('2025-06-01 14:00:00', 1001, 1010, 1002, 1000),
('2025-06-01 14:00:00', 1001, 1011, 1002, 1000),
('2025-06-01 14:00:00', 1001, 1012, 1002, 1000),
('2025-06-01 14:00:00', 1001, 1013, 1003, 1000),
-- mike votes (Dos Supporter)
('2025-06-01 14:00:00', 1002, 1007, 1000, 1000),
('2025-06-01 14:00:00', 1002, 1008, 1001, 1000),
('2025-06-01 14:00:00', 1002, 1009, 1002, 1000),
('2025-06-01 14:00:00', 1002, 1010, 1002, 1000),
('2025-06-01 14:00:00', 1002, 1011, 1002, 1000),
('2025-06-01 14:00:00', 1002, 1012, 1002, 1000),
('2025-06-01 14:00:00', 1002, 1013, 1003, 1000),
-- rachel votes (Uno Suporter)
('2025-06-01 14:00:00', 1003, 1000, 1000, 1000),
('2025-06-01 14:00:00', 1003, 1001, 1001, 1000),
('2025-06-01 14:00:00', 1003, 1002, 1002, 1000),
('2025-06-01 14:00:00', 1003, 1003, 1002, 1000),
('2025-06-01 14:00:00', 1003, 1004, 1002, 1000),
('2025-06-01 14:00:00', 1003, 1005, 1002, 1000),
('2025-06-01 14:00:00', 1003, 1006, 1003, 1000),
-- harvey votes (Uno Suporter)
('2025-06-01 14:00:00', 1004, 1000, 1000, 1000),
('2025-06-01 14:00:00', 1004, 1001, 1001, 1000),
('2025-06-01 14:00:00', 1004, 1002, 1002, 1000),
('2025-06-01 14:00:00', 1004, 1003, 1002, 1000),
('2025-06-01 14:00:00', 1004, 1004, 1002, 1000),
('2025-06-01 14:00:00', 1004, 1005, 1002, 1000),
('2025-06-01 14:00:00', 1004, 1006, 1003, 1000);
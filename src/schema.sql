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
) ENGINE = InnoDB AUTO_INCREMENT = 2024000000;

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
INSERT INTO Roles (role_id, role_name) VALUES ('3000', 'master_admin');  -- role_id: 3000
INSERT INTO Roles (role_id, role_name) VALUES ('3001', 'election_admin');  -- role_id: 3001
INSERT INTO Roles (role_id, role_name) VALUES ('3002', 'voters_admin');  -- role_id: 3002
INSERT INTO Roles (role_id, role_name) VALUES ('1000', 'student_voter');  -- role_id: 1000

-- Users: Admin (user_id: 1000)
INSERT INTO Users (username, email, password, role_id, activated_status) VALUES
('tephL', 'tephL@example.com', '1234567878', 3000, 1), 
('justine', 'justine@example.com', '1234567878', 3001, 1), 
('sandrara', 'sandra@example.com', '1234567878', 3001, 1),
('luigicat', 'luigi@example.com', '1234567878', 3002, 1),
('johnpaul', 'jp@example.com', '1234567878', 3002, 1),
('markjoseph', 'mj@example.com', '1234567878', 3002, 1); 

-- Users: Student Voters (user_id: 1001–1016)
INSERT INTO Users (username, email, password, role_id) VALUES
('juan.delacruz',    'juan@example.com',       '12345678', 1000),
('maria.garcia',     'maria@example.com',      '12345678', 1000),
('carlos.lopez',     'carlos@example.com',     '12345678', 1000),
('ana.martinez',     'ana@example.com',        '12345678', 1000),
('jose.rodriguez',   'jose@example.com',       '12345678', 1000),
('luisa.hernandez',  'luisa@example.com',      '12345678', 1000),
('miguel.gonzales',  'miguel@example.com',     '12345678', 1000),
('sofia.perez',      'sofia@example.com',      '12345678', 1000),
('ramon.castillo',   'ramon@example.com',      '12345678', 1000),
('elena.morales',    'elena@example.com',      '12345678', 1000),
('diego.navarro',    'diego@example.com',      '12345678', 1000),
('isabella.reyes',   'isabella@example.com',   '12345678', 1000),
('marco.santiago',   'marco@example.com',      '12345678', 1000),
('gabrielle.valdez', 'gabrielle@example.com',  '12345678', 1000),
('rafael.salazar',   'rafael@example.com',     '12345678', 1000),
('camille.fuentes',  'camille@example.com',    '12345678', 1000);

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
  `student_id`   INT NOT NULL,
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

-- Elections (election_id: 1000)
INSERT INTO Elections (election_title, status, start_date, end_date) VALUES
('Student Council Election 2025', 'active', '2025-06-01 08:00:00', '2025-06-01 17:00:00');
INSERT INTO Elections (election_title, status, start_date, end_date) VALUES
('Student Council Election 2024', 'completed', '2024-06-01 08:00:00', '2024-06-01 17:00:00');

-- Positions (position_id: 1000–1003)
INSERT INTO Positions (position_name, max_votes, election_id) VALUES
('President', 1,      1000),                               -- position_id: 1000
('Vice-President', 1, 1000),                               -- position_id: 1001
('Senator', 4,       1000),                               -- position_id: 1002
('Vice-Governor', 1,  1000),                               -- position_id: 1003
('Tung Master', 1,      1001),                               -- position_id: 1000
('Brainrot Lord', 2, 1001),                               -- position_id: 1001
('Bracolocococo', 4,        1001);                                -- position_id: 1002

-- PoliticalParties (party_id: 1000–1001)
INSERT INTO PoliticalParties (party_name, election_id) VALUES
('Partido Uno', 1000),                                  -- partylist_id: 1000
('Partido Dos', 1000);                                  -- partylist_id: 1001

-- Candidates (candidate_id: 1000–1013)
INSERT INTO Candidates (party_id, student_id, election_id, position_id) VALUES
-- Partido Uno (party_id: 1000)
(1000, 2024000000, 1000, 1000),  -- candidate_id: 1000 | Juan Dela Cruz   -> President
(1000, 2024000001, 1000, 1001),  -- candidate_id: 1001 | Maria Garcia     -> Vice-President
(1000, 2024000002, 1000, 1002),  -- candidate_id: 1002 | Carlos Lopez     -> Senator
(1000, 2024000003, 1000, 1002),  -- candidate_id: 1003 | Ana Martinez     -> Senator
(1000, 2024000004, 1000, 1002),  -- candidate_id: 1004 | Jose Rodriguez   -> Senator
(1000, 2024000005, 1000, 1003),  -- candidate_id: 1005 | Luisa Hernandez  -> Vice-Governor
(1000, 2024000006, 1000, 1002),  -- candidate_id: 1006 | Miguel Gonzales  -> Senator
(1001, 2024000008, 1000, 1000),  -- candidate_id: 1007 | Ramon Castillo   -> President
(1001, 2024000009, 1000, 1001),  -- candidate_id: 1008 | Elena Morales    -> Vice-President
(1001, 2024000010, 1000, 1002),  -- candidate_id: 1009 | Diego Navarro    -> Senator
(1001, 2024000011, 1000, 1002),  -- candidate_id: 1010 | Isabella Reyes   -> Senator
(1001, 2024000012, 1000, 1002),  -- candidate_id: 1011 | Marco Santiago   -> Senator
(1001, 2024000013, 1000, 1003),  -- candidate_id: 1012 | Gabrielle Valdez -> Vice-Governor
(1001, 2024000015, 1000, 1002); 

-- ==========================================================
-- TESTING FOR ELECTION FORM (exclude on initialization)
-- ==========================================================

-- Students
INSERT INTO Students (first_name, middle_name, last_name, college_id) VALUES
('Tungtung', 'Tung', 'Sahur', 1001), -- 2024000016
('Traralero', 'Tra', 'Lala', 1001),
('Cappucinna', 'Baller', 'Rina', 1001),
('Brocoloco', 'Coco', 'Loco', 1001),
('Brim', 'Bim', 'Patapim', 1001),
('Walter', 'Not', 'White', 1001),
('John', 'Beef', 'Pork', 1001),
('Boyni', 'Bono', 'Nini', 1001); -- 2024000023

-- Election
INSERT INTO Elections (election_title, status, start_date, end_date) VALUES
('Brainrot Showdown 2026', 'active', '2026-03-01 08:00:00', '2026-04-25 17:00:00'); -- 1002

-- Parties
INSERT INTO PoliticalParties (party_name, election_id) VALUES
('Giga Chads', 1002), -- 1002
('Axis', 1002); -- 1003

-- Positions
INSERT INTO Positions (position_name, max_votes, election_id) VALUES
('Brainrot', 1, 1002), -- 1007
('Bawlsacks', 1, 1002), -- 1008
('LowTiers', 3, 1002);  -- 1009

-- Candidates
INSERT INTO Candidates (party_id, student_id, election_id, position_id) VALUES
  -- brainrot
(1002, 2024000016, 1002, 1007),
(1003, 2024000017, 1002, 1007),
  -- bawlsacks
(1002, 2024000018, 1002, 1008),
(1003, 2024000019, 1002, 1008),
  -- lowtiers
(1002, 2024000020, 1002, 1009),
(1002, 2024000021, 1002, 1009),
(1003, 2024000022, 1002, 1009),
(1003, 2024000023, 1002, 1009);


-- ========================= JOINS FOR SEEING VOTES OF A PERSON
SELECT 
	v.studentvoter_id,
    s.last_name,
    p.position_name
FROM Votes v
LEFT JOIN Candidates c
	ON v.candidate_id = c.candidate_id
LEFT JOIN Positions p
	ON p.position_id = c.position_id
LEFT JOIN Students s
	ON s.student_id = c.student_id


-- ============ pagination example
SELECT * FROM users LIMIT 10 OFFSET 0;  -- page 1
SELECT * FROM users LIMIT 10 OFFSET 10; -- page 2
SELECT * FROM users LIMIT 10 OFFSET 20; -- page 3

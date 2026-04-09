
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



INSERT INTO Votes (studentvoter_id, candidate_id, position_id, election_id) VALUES
-- Partido Uno supporters: Juan Dela Cruz (candidate_id: 1000)
(1000, 1000, 1000, 1000),
(1001, 1000, 1000, 1000),
(1002, 1000, 1000, 1000),
(1003, 1000, 1000, 1000),
(1004, 1000, 1000, 1000),
(1005, 1000, 1000, 1000),
(1006, 1000, 1000, 1000),
(1007, 1000, 1000, 1000),
(1008, 1000, 1000, 1000),
(1009, 1000, 1000, 1000),
-- Partido Dos supporters: Ramon Castillo (candidate_id: 1007)
(1010, 1007, 1000, 1000),
(1011, 1007, 1000, 1000),
(1012, 1007, 1000, 1000),
(1013, 1007, 1000, 1000),
(1014, 1007, 1000, 1000),
(1015, 1007, 1000, 1000);

-- ============================================================
-- POSITION 1001: VICE-PRESIDENT (max_votes: 1)
-- Winner: Maria Garcia (Partido Uno) - 11 votes (68.75%)
-- ============================================================
INSERT INTO Votes (studentvoter_id, candidate_id, position_id, election_id) VALUES
-- Partido Uno supporters: Maria Garcia (candidate_id: 1001)
(1000, 1001, 1001, 1000),
(1001, 1001, 1001, 1000),
(1002, 1001, 1001, 1000),
(1003, 1001, 1001, 1000),
(1004, 1001, 1001, 1000),
(1005, 1001, 1001, 1000),
(1006, 1001, 1001, 1000),
(1007, 1001, 1001, 1000),
(1008, 1001, 1001, 1000),
(1009, 1001, 1001, 1000),
(1010, 1001, 1001, 1000),  -- Swing voter
-- Partido Dos supporters: Elena Morales (candidate_id: 1008)
(1011, 1008, 1001, 1000),
(1012, 1008, 1001, 1000),
(1013, 1008, 1001, 1000),
(1014, 1008, 1001, 1000),
(1015, 1008, 1001, 1000);

-- ============================================================
-- POSITION 1002: SENATOR (max_votes: 4, multiple choice per voter)
-- Partido Uno Candidates: 1002, 1003, 1004, 1006 (vote once per voter)
-- Partido Dos Candidates: 1009, 1010, 1011, 1013 (vote once per voter)
-- Expected: Partido Uno dominates legislative positions
-- ============================================================
INSERT INTO Votes (studentvoter_id, candidate_id, position_id, election_id) VALUES
-- Voter 1000: Partido Uno preference (Carlos Lopez)
(1000, 1002, 1002, 1000),
-- Voter 1001: Partido Uno preference (Ana Martinez)
(1001, 1003, 1002, 1000),
-- Voter 1002: Partido Uno preference (Jose Rodriguez)
(1002, 1004, 1002, 1000),
-- Voter 1003: Partido Uno preference (Miguel Gonzales)
(1003, 1006, 1002, 1000),
-- Voter 1004: Partido Uno preference (Carlos Lopez)
(1004, 1002, 1002, 1000),
-- Voter 1005: Partido Uno preference (Ana Martinez)
(1005, 1003, 1002, 1000),
-- Voter 1006: Partido Uno preference (Jose Rodriguez)
(1006, 1004, 1002, 1000),
-- Voter 1007: Partido Uno preference (Miguel Gonzales)
(1007, 1006, 1002, 1000),
-- Voter 1008: Partido Uno preference (Carlos Lopez)
(1008, 1002, 1002, 1000),
-- Voter 1009: Partido Uno preference (Ana Martinez)
(1009, 1003, 1002, 1000),
-- Voter 1010: Partido Dos preference (Diego Navarro)
(1010, 1009, 1002, 1000),
-- Voter 1011: Partido Dos preference (Isabella Reyes)
(1011, 1010, 1002, 1000),
-- Voter 1012: Partido Dos preference (Marco Santiago)
(1012, 1011, 1002, 1000),
-- Voter 1013: Partido Dos preference (Camille Fuentes)
(1013, 1013, 1002, 1000),
-- Voter 1014: Partido Dos preference (Diego Navarro)
(1014, 1009, 1002, 1000),
-- Voter 1015: Partido Dos preference (Isabella Reyes)
(1015, 1010, 1002, 1000);

INSERT INTO Votes (studentvoter_id, candidate_id, position_id, election_id) VALUES
-- Partido Uno supporters: Luisa Hernandez (candidate_id: 1005)
(1000, 1005, 1003, 1000),
(1001, 1005, 1003, 1000),
(1002, 1005, 1003, 1000),
(1003, 1005, 1003, 1000),
(1004, 1005, 1003, 1000),
(1005, 1005, 1003, 1000),
(1006, 1005, 1003, 1000),
(1007, 1005, 1003, 1000),
-- Partido Dos supporters: Gabrielle Valdez (candidate_id: 1012)
(1008, 1012, 1003, 1000),
(1009, 1012, 1003, 1000),
(1010, 1012, 1003, 1000),
(1011, 1012, 1003, 1000),
(1012, 1012, 1003, 1000),
(1013, 1012, 1003, 1000),
(1014, 1012, 1003, 1000),
(1015, 1012, 1003, 1000);

UPDATE StudentVoters SET has_voted = 1 WHERE studentvoter_id BETWEEN 1000 AND 1015;

-- =============================
-- Template (Ignore)
-- =============================
DELIMITER //

CREATE PROCEDURE RegisterUser(
    IN  input_id INT,
    IN  input_name VARCHAR(255)
)
BEGIN
    INSERT INTO Users VALUES (input_id, input_name);
END //

DELIMITER ;

CALL RegisterUser(0, 'Stephen');


-- =============================
-- Reminder: For those of u that'll encounter an issue with executing these procedures
-- 
-- A. If Linux
-- 1. sudo /opt/lampp/bin/mysql_upgrade -u root -p
-- 2. sudo /opt/lampp/xampp restart
--
-- B. If Windows
-- 1. Open CMD as an Administrator
-- 2. C:\xampp\mysql\bin\mysql_upgrade.exe -u root -p
-- 2.1. Password will normally be empty so just click Enter. Otherwise, chant for Tung Tung Tung Sahur  
-- =============================


-- ====================================
-- StudentVoter/User Registration
-- ====================================

DELIMITER //
CREATE PROCEDURE RegisterStudent(
    IN input_username VARCHAR(255),
    IN input_email    VARCHAR(255),
    IN input_password VARCHAR(255),
    IN input_student_id INT
)
BEGIN
    DECLARE new_user_id INT;

    INSERT INTO Users (username, email, password, role_id)
    VALUES (input_username, input_email, input_password, 1000);

    SET new_user_id = LAST_INSERT_ID();

    INSERT INTO StudentVoters (user_id, student_id)
    VALUES (new_user_id, input_student_id);
END //
DELIMITER ;

CALL RegisterStudent(username, email, password, student_id);
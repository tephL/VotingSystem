-- ====================================
-- StudentVoter/User Accept
-- ====================================

DELIMITER //
CREATE PROCEDURE AcceptUser(
    IN input_user_id INT
)
BEGIN
    UPDATE Users SET activated_status = 1 WHERE user_id = input_user_id;
END //
DELIMITER ;

-- CALL AcceptUser(user_id);


-- ====================================
-- StudentVoter/User Rejection
-- ====================================

DELIMITER //
CREATE PROCEDURE DeleteUser(
    IN input_user_id INT
)
BEGIN
    DELETE FROM StudentVoters WHERE user_id = input_user_id;
    DELETE FROM Users WHERE user_id = input_user_id;
END //
DELIMITER ;

-- CALL DeleteUser(user_id);


-- ====================================
-- Updating a StudentVoter/User info
-- ====================================

DELIMITER //
CREATE PROCEDURE UpdateStudentVoterInfo(
    IN input_user_id INT,
    IN input_username VARCHAR(255),
    IN input_email VARCHAR(255),
    IN input_password VARCHAR(255),
    IN input_activated_status INT,
    IN input_student_id BIGINT
)
BEGIN
    UPDATE Users 
    SET 
        username = input_username,
        email = input_email,
        password = input_password,
        activated_status = input_activated_status
    WHERE user_id = input_user_id;

    UPDATE StudentVoters
    SET
        student_id = input_student_id
    WHERE user_id = input_user_id;
END //
DELIMITER ;

-- CALL UpdateStudentVoterInfo(user_id, username, email, password, activated_status, student_id);

-- ====================================
-- Updating a AdminUser info
-- ====================================

DELIMITER //
CREATE PROCEDURE UpdateAdminInfo(
    IN input_user_id INT,
    IN input_role_id INT,
    IN input_username VARCHAR(255),
    IN input_email VARCHAR(255),
    IN input_password VARCHAR(255),
    IN input_activated_status INT,

    IN input_first_name VARCHAR(255),
    IN input_middle_name VARCHAR(255),
    IN input_last_name VARCHAR(255),
    IN input_contact_number VARCHAR(20)
)
BEGIN
    UPDATE Users 
    SET 
        role_id = input_role_id,
        username = input_username,
        email = input_email,
        password = input_password,
        activated_status = input_activated_status
    WHERE user_id = input_user_id;

    UPDATE Admins
    SET
        first_name = input_first_name,
        middle_name = input_middle_name,
        last_name = input_last_name,
        contact_number = input_contact_number
    WHERE user_id = input_user_id;
END //
DELIMITER ;

-- CALL UpdateStudentVoterInfo(user_id, username, email, password, activated_status, student_id);

-- ====================================
-- Admin Deletion
-- ====================================

DELIMITER //
CREATE PROCEDURE DeleteAdmin(
    IN input_user_id INT
)
BEGIN
    DELETE FROM Admins WHERE user_id = input_user_id;
    DELETE FROM Users WHERE user_id = input_user_id;
END //
DELIMITER ;

-- CALL DeleteAdmin(user_id);

-- ====================================
-- Admin Creation 
-- ====================================

DELIMITER //
CREATE PROCEDURE RegisterAdmin(
    IN infirst_name VARCHAR(255),
    IN inmiddle_name VARCHAR(255),
    IN inlast_name VARCHAR(255),
    IN incontact_number VARCHAR(20),
    IN inemail VARCHAR(255),
    IN inusername VARCHAR(255),
    IN inpassword VARCHAR(255),
    IN inrole_id INT
)
BEGIN
    INSERT INTO Users(email, username, password, role_id) VALUES (inemail, inusername, inpassword, inrole_id);
    INSERT INTO Admins(first_name, middle_name, last_name, contact_number, user_id) VALUES (infirst_name, inmiddle_name, inlast_name, incontact_number, last_insert_id());
END //
DELIMITER ;

-- CALL RegisterAdmin($first_name, $middle_name, $last_name, $contact_number, $email, $username, $password, $role_id)
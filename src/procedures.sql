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

CALL AcceptUser(user_id);


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

CALL DeleteUser(user_id);


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

CALL UpdateStudentVoterInfo(user_id, username, email, password, activated_status, student_id);

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
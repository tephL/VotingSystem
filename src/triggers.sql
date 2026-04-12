DELIMITER $$

CREATE TRIGGER check_election_active
BEFORE INSERT ON Votes
FOR EACH ROW
BEGIN
    DECLARE v_status VARCHAR(20);
    DECLARE v_start DATETIME;
    DECLARE v_end DATETIME;

    SELECT status, start_date, end_date
    INTO v_status, v_start, v_end
    FROM Elections
    WHERE election_id = NEW.election_id;

    IF v_status != 'active' OR NOW() < v_start OR NOW() > v_end THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Election is not active';
    END IF;
END $$

DELIMITER ;

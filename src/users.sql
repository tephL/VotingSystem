

CREATE USER 'votingapp'@'localhost' IDENTIFIED BY 'yourpassword';

-- Grant specific privileges on your DB
GRANT SELECT, INSERT, UPDATE, DELETE ON votingsystem.* TO 'votingapp'@'localhost';

-- Apply changes
FLUSH PRIVILEGES;


DROP USER 'votingapp'@'localhost';
FLUSH PRIVILEGES;

-- Voter Role
CREATE USER 'Voter'@'localhost' IDENTIFIED BY '';
GRANT SELECT, INSERT ON VotingSystem.Votes TO 'Voter'@'localhost';
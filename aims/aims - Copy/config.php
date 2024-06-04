<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $conn = mysqli_connect($servername, $username, $password);
    if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    }
    $sql = "CREATE DATABASE IF NOT EXISTS ilpsystem";
    if (mysqli_query($conn, $sql)) {
    }
    else {
    echo "Error creating database: " . mysqli_error($conn);
    }
    mysqli_close($conn);


?>

<?php 

$servername = "localhost"; 
$username = "root"; 
$password = "";
$dbname = "ilpsystem";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sqlT = "CREATE TABLE IF NOT EXISTS teams (
    teamId INT NOT NULL PRIMARY KEY,
    teamName VARCHAR(255) NOT NULL,
    image BLOB NOT NULL
)";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS game_records (
    grecord_ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    gID VARCHAR (255) NOT NULL, 
    eventName VARCHAR(255) NOT NULL,
    teamName VARCHAR(255) NOT NULL,
    score DECIMAL(10, 2) NOT NULL,
    points INT NOT NULL
)";


if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS leaderboard (
    lbID INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    teamID INT NOT NULL,
    teamName VARCHAR(255) NOT NULL,
    points DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (teamID) REFERENCES teams(teamID)
)";


if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS events (
    eventID INT PRIMARY KEY NOT NULL,
    eventName VARCHAR(255) NOT NULL,
    eventType VARCHAR(255) NOT NULL,
    eventCategory VARCHAR(255) NOT NULL
)";


if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS participants (
    partID INT PRIMARY KEY NOT NULL,
    partName VARCHAR(255) NOT NULL,
    teamName VARCHAR(255) NOT NULL,
    eventName VARCHAR(255) NOT NULL,
    eventType VARCHAR(255) NOT NULL
)";


if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS accounts (
    userId VARCHAR(255) NOT NULL PRIMARY KEY,
    firstName VARCHAR(255) NOT NULL,
    middleName VARCHAR(255),
    lastName VARCHAR(255) NOT NULL,
    suffix VARCHAR(50),
    password VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS eventContestant (
    teamId INT,
    eventType VARCHAR(255) NOT NULL,
    eventName VARCHAR(255) NOT NULL,
    PRIMARY KEY (teamId, eventName),
    FOREIGN KEY (teamId) REFERENCES teams(teamId)
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS eventFaci (
    faciId VARCHAR(255),
    eventType VARCHAR(255) NOT NULL,
    eventName VARCHAR(255) NOT NULL,
    PRIMARY KEY (faciId, eventName),
    FOREIGN KEY (faciId) REFERENCES accounts(userId)
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS eventJudge (
    judgeId VARCHAR(255),
    eventType VARCHAR(255) NOT NULL,
    eventName VARCHAR(255) NOT NULL,
    PRIMARY KEY (judgeId, eventName),
    FOREIGN KEY (judgeId) REFERENCES accounts(userId)
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS eventScoring (
    rank VARCHAR(255),
    eventCategory VARCHAR(255) NOT NULL,
    points INT NOT NULL,
    PRIMARY KEY (rank, eventCategory)
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS eventCriteria (
    criteriaId INT AUTO_INCREMENT,
    criteria VARCHAR(255),
    points INT NOT NULL,
    eventName VARCHAR(255),
    PRIMARY KEY (criteriaId)
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS admin (
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY (username, password)
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

include 'encryption.php';
#sensitive info, need encryption
$admin = array();
$sqlS = "SELECT * FROM admin;";
if ($retval = mysqli_query($conn, $sqlS)) {
    if (mysqli_num_rows($retval) > 0) {
        while ($row = mysqli_fetch_assoc($retval)) {
            $user = $row['username'];
            $pass = $row['password'];

            $admin[] = array("username" => $user, "password" => $pass);
        }
    }
} else {
    echo "Error executing SELECT query: " . $conn->error;
}

$adminUsername = 'admin123';
$adminPass = 'us3p@admin';
$adminExists = false;

foreach ($admin as $acc) {
    $decryptedname = decrypt($acc['username'], $encryption_key);
    $decryptedpass = decrypt($acc['password'], $encryption_key);

    if ($decryptedname == $adminUsername && $decryptedpass == $adminPass) {
        $adminExists = true;
        break;
    }
}

if (!$adminExists) {
    $encryptedUsername = encrypt($adminUsername, $encryption_key);
    $encryptedPass = encrypt($adminPass, $encryption_key);

    $sqlAdd = "INSERT INTO admin (username, password) VALUES ('$encryptedUsername', '$encryptedPass')";
    if ($conn->query($sqlAdd) === TRUE) {
    } else {
        echo "Error inserting new admin user: " . $conn->error;
    }
}

$sqlT = "CREATE TABLE IF NOT EXISTS adminlogs (
    logId INT AUTO_INCREMENT PRIMARY KEY,
    date_on DATETIME NOT NULL,
    actions VARCHAR(255) NOT NULL
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}


#TRIGGERS (ADMIN ALL) --------------------------------------------------------------------------------------------------------
$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_addGRecord
        AFTER INSERT ON game_records
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Game record ', NEW.gID,' added in ', NEw.eventName);

            INSERT INTO adminlogs (date_on, actions)
            VALUES (NOW(), actions);
        END ;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_delCriteria
        AFTER DELETE ON eventCriteria 
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Deleted criteria in ', OLD.eventName);

            INSERT INTO adminlogs (date_on, actions)
            VALUES (NOW(), actions);
        END ;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_editCriteria
        AFTER UPDATE ON eventCriteria 
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Updated criteria in ', NEW.eventName);

            INSERT INTO adminlogs (date_on, actions)
            VALUES (NOW(), actions);
        END ;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_addCriteria
        AFTER INSERT ON eventCriteria 
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Added criteria in ', NEW.eventName);

            INSERT INTO adminlogs (date_on, actions)
            VALUES (NOW(), actions);
        END ;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_addScoring
        AFTER INSERT ON eventScoring
        FOR EACH ROW
        BEGIN
            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), 'Added a scoring.');
        END;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_delScore
        AFTER DELETE ON eventScoring
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Deleted a scoring in rank: ', OLD.rank);

            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), actions);
        END;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_addFaci
        AFTER INSERT ON eventFaci
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            DECLARE event_name VARCHAR(255);
    
            SELECT eventName INTO event_name FROM eventFaci WHERE faciId = NEW.faciId AND eventName = NEW.eventName;
            SET actions = CONCAT('Added a facilitator in ', event_name);

            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), actions);
        END;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_delFaci
        AFTER DELETE ON eventFaci
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Deleted a facilitator in ', OLD.eventName);

            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), actions);
        END;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_addJudge
        AFTER INSERT ON eventJudge
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            DECLARE event_name VARCHAR(255);
    
            SELECT eventName INTO event_name FROM eventJudge WHERE judgeId = NEW.judgeId AND eventName = NEW.eventName;
            SET actions = CONCAT('Added a judge in ', event_name);

            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), actions);
        END;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_delJudge
        AFTER DELETE ON eventJudge
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Deleted a judge in ', OLD.eventName);

            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), actions);
        END;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_addContestant
        AFTER INSERT ON eventContestant
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            DECLARE event_name VARCHAR(255);
    
            SELECT eventName INTO event_name FROM eventContestant WHERE teamId = NEW.teamId AND eventName = NEW.eventName;
            SET actions = CONCAT('Added a contestant in ', event_name);

            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), actions);
        END;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_delContestant
        AFTER DELETE ON eventContestant
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Deleted a contestant in ', OLD.eventName);

            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), actions);
        END;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_addAcc
        AFTER INSERT ON accounts
        FOR EACH ROW
        BEGIN
            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), 'Added account');
        END;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_editAcc
        AFTER UPDATE ON accounts
        FOR EACH ROW
        BEGIN
            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), 'Updated an account.');
        END";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_delAcc
        AFTER DELETE ON accounts
        FOR EACH ROW
        BEGIN
            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), 'Deleted an account.');
        END";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_addTeam
        AFTER INSERT ON teams
        FOR EACH ROW
        BEGIN
            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), 'Added a team.');
        END;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_editTeam
        AFTER UPDATE ON teams
        FOR EACH ROW
        BEGIN
            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), 'Updated a team.');
        END";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_delTeam
        AFTER DELETE ON teams
        FOR EACH ROW
        BEGIN
            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), 'Deleted a team.');
        END";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_addEvent
        AFTER INSERT ON events
        FOR EACH ROW
        BEGIN
            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), 'Added an event.');
        END";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_editEvent
        AFTER UPDATE ON events
        FOR EACH ROW
        BEGIN
            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), 'Updated an event.');
        END";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_delEvent
        AFTER DELETE ON events
        FOR EACH ROW
        BEGIN
            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), 'Deleted an event.');
        END";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_addParti
        AFTER INSERT ON participants
        FOR EACH ROW
        BEGIN
            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), 'Added a participant.');
        END";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}



#VIEWS (ADMIN ALL) --------------------------------------------------------------------------------------------------------
$sqlF = "CREATE OR REPLACE VIEW vw_criteria AS SELECT * FROM eventCriteria;";
if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_gRecord AS SELECT * FROM game_records";
if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_leaderboard AS
        SELECT eventName, teamName, SUM(points) as points FROM game_records GROUP BY 
        teamName ORDER BY points DESC;";
if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_accounts AS SELECT * FROM accounts";
if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_admin AS SELECT * FROM admin";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_teams AS SELECT * FROM teams";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_events AS SELECT * FROM events";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_participants AS SELECT * FROM participants";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_eventParti AS SELECT * FROM eventContestant";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_eventFaci AS SELECT * FROM eventFaci";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_eventJudge AS SELECT * FROM eventJudge";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_eventScore AS SELECT * FROM eventScoring";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}



#PROCEDURES (ADMIN ALL) --------------------------------------------------------------------------------------------------------
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getPartiFrom(IN evname VARCHAR(255))
        BEGIN
            SELECT *, teamName FROM vw_eventparti INNER JOIN vw_teams ON 
            vw_teams.teamId = vw_eventparti.teamId WHERE eventName = evname;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getRecordFrom(IN evname VARCHAR(255))
        BEGIN
            SELECT teamName, SUM(points) as points FROM vw_gRecord WHERE 
            eventName = evname GROUP BY teamName ORDER BY points DESC;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertGRecord(IN gid VARCHAR(255), IN evname VARCHAR(255),
IN tname VARCHAR(255), IN sc DECIMAL, IN pts INT)
        BEGIN
            INSERT INTO vw_gRecord (gID, eventName, teamName, score, points) VALUES 
            (gid, evname, tname, sc, pts);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getLeaderboard()
        BEGIN
            SELECT * FROM vw_leaderboard ORDER BY points DESC;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAllAcc()
        BEGIN
            SELECT * FROM vw_accounts;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAnAcc(IN id VARCHAR(255))
        BEGIN
            SELECT * FROM vw_accounts WHERE userId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAcc(IN limit_num INT)
        BEGIN
            SELECT * FROM vw_accounts LIMIT limit_num;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertAcc(IN userId VARCHAR(255), IN firstName VARCHAR(255),
        IN middleName VARCHAR(255), IN lastName VARCHAR(255), IN suffix VARCHAR(50), IN password VARCHAR(255),
        IN type VARCHAR(50))
        BEGIN
            INSERT INTO vw_accounts VALUES (userId, firstName, middleName, lastName, suffix, password, type);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_editAcc(IN id VARCHAR(255), IN fn VARCHAR(255),
        IN mn VARCHAR(255), IN ln VARCHAR(255), IN sfx VARCHAR(50), IN pass VARCHAR(255),
        IN acctype VARCHAR(50))
        BEGIN
            UPDATE vw_accounts SET firstName = fn, middleName = mn, lastName = ln, suffix = sfx, 
            password = pass, type = acctype WHERE userId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delAcc(IN id VARCHAR(255))
        BEGIN
            DELETE FROM vw_accounts WHERE userId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAccType(IN ptype VARCHAR(255))
        BEGIN
            SELECT * FROM vw_accounts WHERE type = ptype;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAdmin()
        BEGIN
            SELECT * FROM vw_admin;
        END ;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getTeam(IN limit_num INT, IN offset INT)
        BEGIN
            SELECT * FROM vw_teams LIMIT limit_num OFFSET offset;
        END ;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getTeamCount()
        BEGIN
            SELECT COUNT(*) AS total FROM vw_teams;
        END ;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getATeam(IN id VARCHAR(255))
        BEGIN
            SELECT * FROM vw_teams WHERE teamId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertTeam(IN id INT, IN name VARCHAR(255), IN img BLOB)
        BEGIN
            INSERT INTO vw_teams VALUES (id, name, img);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_editTeam(IN id INT, IN img BLOB, IN name VARCHAR(255))
        BEGIN
            UPDATE vw_teams SET teamName = name, image = img WHERE teamId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_editTeamName(IN id INT, IN name VARCHAR(255))
        BEGIN
            UPDATE vw_teams SET teamName = name WHERE teamId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delTeam(IN id INT)
        BEGIN
            DELETE FROM vw_teams WHERE teamId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAllTeam()
        BEGIN
            SELECT * FROM vw_teams;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEvents()
        BEGIN
            SELECT * FROM vw_events;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventFrom(IN type VARCHAR(255))
        BEGIN
            SELECT * FROM vw_events WHERE eventType = type;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEvent(IN id INT)
        BEGIN
            SELECT * FROM vw_events WHERE eventID = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventLimit(IN limitnum INT)
        BEGIN
            SELECT * FROM vw_events LIMIT limitnum;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEvent(IN id INT, IN name VARCHAR(255), 
        IN evtype VARCHAR(255), IN category VARCHAR(255))
        BEGIN
            INSERT INTO vw_events VALUES (id, name, evtype, category);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delEvent(IN id INT)
        BEGIN
            DELETE FROM vw_events WHERE eventID = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_editEvent(IN id INT, IN type VARCHAR(255), IN name VARCHAR(255), IN category VARCHAR(255))
        BEGIN
            UPDATE vw_events SET eventType = type, eventName = name, eventCategory = category WHERE eventID = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getParticipant(IN id INT)
        BEGIN
            SELECT * FROM vw_participants WHERE partID = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_addPart(IN id INT, IN name VARCHAR(255), 
        IN team VARCHAR(255), IN event VARCHAR(255), IN type VARCHAR(255))
        BEGIN
            INSERT INTO vw_participants VALUES (id, name, team, event, type);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getContestant(IN id INT, IN type VARCHAR(255), IN event VARCHAR(255))
        BEGIN
            SELECT * FROM vw_eventParti WHERE teamId = id AND eventType = type AND eventName = event;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventContestant(IN event VARCHAR(255), IN type VARCHAR(255))
        BEGIN
            SELECT vw_teams.teamId, teamName FROM vw_eventParti INNER JOIN vw_teams ON 
            vw_teams.teamId = vw_eventParti.teamId WHERE eventType = event AND eventName = type;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEventContestant(IN id INT, 
        IN evtype VARCHAR(255), IN evname VARCHAR(255))
        BEGIN
            INSERT INTO vw_eventParti VALUES (id, evtype, evname);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delEventContestant(IN id INT, IN event VARCHAR(255))
        BEGIN
            DELETE FROM vw_eventParti WHERE teamId = id AND eventName = event;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventFaci(IN event VARCHAR(255), IN type VARCHAR(255))
        BEGIN
            SELECT vw_accounts.userId, firstName FROM vw_eventFaci INNER JOIN vw_accounts ON 
            vw_accounts.userId = vw_eventFaci.faciId WHERE eventType = event AND eventName = type;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventJudge(IN event VARCHAR(255), IN type VARCHAR(255))
        BEGIN
            SELECT vw_accounts.userId, firstName FROM vw_eventJudge INNER JOIN vw_accounts ON 
            vw_accounts.userId = vw_eventJudge.judgeId WHERE eventType = event AND eventName = type;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getFaci(IN id VARCHAR(255), IN type VARCHAR(255), IN event VARCHAR(255))
        BEGIN
            SELECT * FROM vw_eventFaci WHERE faciId = id AND eventType = type AND eventName = event;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getJudge(IN id VARCHAR(255), IN type VARCHAR(255), IN event VARCHAR(255))
        BEGIN
            SELECT * FROM vw_eventJudge WHERE judgeId = id AND eventType = type AND eventName = event;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEventFaci(IN id VARCHAR(255), 
        IN evtype VARCHAR(255), IN evname VARCHAR(255))
        BEGIN
            INSERT INTO vw_eventFaci VALUES (id, evtype, evname);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEventJudge(IN id VARCHAR(255), 
        IN evtype VARCHAR(255), IN evname VARCHAR(255))
        BEGIN
            INSERT INTO vw_eventJudge VALUES (id, evtype, evname);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delEventFaci(IN id VARCHAR(255), IN event VARCHAR(255))
        BEGIN
            DELETE FROM vw_eventFaci WHERE faciId = id AND eventName = event;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delEventJudge(IN id VARCHAR(255), IN event VARCHAR(255))
        BEGIN
            DELETE FROM vw_eventJudge WHERE judgeId = id AND eventName = event;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getScoring()
        BEGIN
            SELECT * FROM vw_eventScore;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getScoringChk(IN rankin VARCHAR(255), IN category VARCHAR(255))
        BEGIN
            SELECT * FROM vw_eventScore WHERE rank = rankin AND eventCategory = category;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertScoring(IN rank VARCHAR(255), IN category VARCHAR(255), IN pts INT)
        BEGIN
            INSERT INTO vw_eventScore VALUES (rank, category, pts);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delScoring(IN rankin VARCHAR(255))
        BEGIN
            DELETE FROM vw_eventScore WHERE rank = rankin;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlP = "CREATE PROCEDURE IF NOT EXISTS sp_getCriteria(IN evname VARCHAR(255))
        BEGIN
            SELECT * FROM vw_criteria WHERE eventName = evname;
        END ;";
if ($conn->query($sqlP) === TRUE) {
} else {
    echo "Error: " . $conn->error;
}

$sqlP = "CREATE PROCEDURE IF NOT EXISTS sp_insertCriteria(IN cri VARCHAR(255),
        IN pts INT, IN evname VARCHAR(255))
        BEGIN
            INSERT INTO vw_criteria (criteria, points, eventName) 
            VALUES (cri, pts, evname);
        END ;";
if ($conn->query($sqlP) === TRUE) {
} else {
    echo "Error: " . $conn->error;
}

$sqlP = "CREATE PROCEDURE IF NOT EXISTS sp_editCriteria(IN id INT, IN cri VARCHAR(255),
        IN pts INT)
        BEGIN
            UPDATE vw_criteria SET criteria = cri, points = pts WHERE criteriaId = id;
        END ;";
if ($conn->query($sqlP) === TRUE) {
} else {
    echo "Error: " . $conn->error;
}

$sqlP = "CREATE PROCEDURE IF NOT EXISTS sp_delCriteria(IN id INT)
        BEGIN
            DELETE FROM vw_criteria WHERE criteriaId = id;
        END ;";
if ($conn->query($sqlP) === TRUE) {
} else {
    echo "Error: " . $conn->error;
}

$conn->close();

?>
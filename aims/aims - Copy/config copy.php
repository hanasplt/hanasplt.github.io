<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $conn = mysqli_connect($servername, $username, $password);
    if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    }
    $sql = "CREATE DATABASE IF NOT EXISTS ilps";
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
$dbname = "ilps";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sqlT = "CREATE TABLE IF NOT EXISTS teams (
    teamId INT AUTO_INCREMENT PRIMARY KEY,
    teamName VARCHAR(255) NOT NULL,
    image LONGBLOB NOT NULL
)";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

/* pending
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
    lbID INT AUTO_INCREMENT PRIMARY KEY,
    eventId
    teamID INT NOT NULL,
    teamName VARCHAR(255) NOT NULL,
    points DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (teamID) REFERENCES teams(teamID)
)";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}
*/

$sqlT = "CREATE TABLE IF NOT EXISTS events (
    eventID INT AUTO_INCREMENT PRIMARY KEY,
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
    userId VARCHAR(255),
    firstName VARCHAR(255) NOT NULL,
    middleName VARCHAR(255),
    lastName VARCHAR(255) NOT NULL,
    suffix VARCHAR(50),
    password VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    log_status VARCHAR(50) NOT NULL,
    reset_token VARCHAR(64),
    PRIMARY KEY (userId)
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS contestant (
    contId INT AUTO_INCREMENT PRIMARY KEY,
    teamId INT NOT NULL,
    teamName VARCHAR(255) NOT NULL,
    eventId INT NOT NULL,
    status VARCHAR(255) #finish when done judging
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS faci (
    faciNo INT AUTO_INCREMENT PRIMARY KEY,
    faciId VARCHAR(255) NOT NULL,
    eventId INT NOT NULL,
    eventName VARCHAR(255) NOT NULL,
    faciName VARCHAR(255) NOT NULL
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS judges (
    judgeNo INT AUTO_INCREMENT PRIMARY KEY,
    judgeId VARCHAR(255) NOT NULL,
    eventId VARCHAR(255) NOT NULL,
    eventName VARCHAR(255) NOT NULL,
    jugdeName VARCHAR(255) NOT NULL
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

#pending
$sqlT = "CREATE TABLE IF NOT EXISTS eventScoring (
    rankNo INT,
    rank VARCHAR(255) NOT NULL,
    eventCategory VARCHAR(255) NOT NULL,
    points INT NOT NULL,
    PRIMARY KEY (rankNo, eventCategory)
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}
#pending

$sqlT = "CREATE TABLE IF NOT EXISTS criteria (
    criteriaId INT AUTO_INCREMENT PRIMARY KEY,
    eventId INT NOT NULL,
    criteria VARCHAR(255) NOT NULL,
    percentage INT NOT NULL
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS sub_results (
    subId INT AUTO_INCREMENT PRIMARY KEY,
    eventId INT NOT NULL,
    contestantId INT NOT NULL,
    personnelId VARCHAR(299) NOT NULL,
    total_score DECIMAL(10, 2) NOT NULL,
    criteria1 DECIMAL(10, 2) NOT NULL,
    criteria2 DECIMAL(10, 2) NOT NULL,
    criteria3 DECIMAL(10, 2) NOT NULL,
    criteria4 DECIMAL(10, 2) NOT NULL,
    criteria5 DECIMAL(10, 2) NOT NULL,
    criteria6 DECIMAL(10, 2) NOT NULL,
    criteria7 DECIMAL(10, 2) NOT NULL,
    criteria8 DECIMAL(10, 2) NOT NULL,
    criteria9 DECIMAL(10, 2) NOT NULL,
    criteria10 DECIMAL(10, 2) NOT NULL
)";

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
/*
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
*/

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_delCriteria
        AFTER DELETE ON criteria 
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Deleted criteria: ', OLD.eventId);

            INSERT INTO adminlogs (date_on, actions)
            VALUES (NOW(), actions);
        END ;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_editCriteria
        AFTER UPDATE ON criteria 
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Updated criteria: ', NEW.eventId);

            INSERT INTO adminlogs (date_on, actions)
            VALUES (NOW(), actions);
        END ;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_addCriteria
        AFTER INSERT ON criteria 
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Added criteria: ', NEW.eventId);

            INSERT INTO adminlogs (date_on, actions)
            VALUES (NOW(), actions);
        END ;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

#pending
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
#pending

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_addFaci
        AFTER INSERT ON faci
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Added a facilitator in ', NEW.eventName);

            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), actions);
        END;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_delFaci
        AFTER DELETE ON faci
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
        AFTER INSERT ON judges
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Added a judge in ', NEW.eventName);

            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), actions);
        END;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_delJudge
        AFTER DELETE ON judges
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
        AFTER INSERT ON contestant
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Added contestant: ', NEW.teamName);

            INSERT INTO adminlogs (date_on, actions) 
            VALUES (NOW(), actions);
        END;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TRIGGER IF NOT EXISTS tr_log_delContestant
        AFTER DELETE ON contestant
        FOR EACH ROW
        BEGIN
            DECLARE actions VARCHAR(255);
            SET actions = CONCAT('Deleted a contestant: ', OLD.eventId);

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
            VALUES (NOW(), 'Added an account');
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



#VIEWS (ADMIN ALL) --------------------------------------------------------------------------------------------------------
$sqlF = "CREATE OR REPLACE VIEW vw_criteria AS SELECT * FROM criteria;";
if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

/*
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
*/

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

$sqlF = "CREATE OR REPLACE VIEW vw_eventParti AS SELECT * FROM contestant";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_eventFaci AS SELECT * FROM faci";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_eventJudge AS SELECT * FROM judges";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

#pending
$sqlF = "CREATE OR REPLACE VIEW vw_eventScore AS SELECT * FROM eventScoring";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}
#pending



#PROCEDURES (ADMIN ALL) --------------------------------------------------------------------------------------------------------

/*
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
*/

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

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertAcc(IN id VARCHAR(255), IN fn VARCHAR(255),
        IN mn VARCHAR(255), IN ln VARCHAR(255), IN sfx VARCHAR(50), IN pass VARCHAR(255),
        IN typ VARCHAR(50))
        BEGIN
            INSERT INTO vw_accounts (userId, firstName, middleName, lastName, suffix, password, type) 
            VALUES (id, fn, mn, ln, sfx, pass, typ);
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

#pending
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_editAccPass(IN id VARCHAR(255), IN pass VARCHAR(255),
        IN stat VARCHAR(50))
        BEGIN
            UPDATE vw_accounts SET password = pass, log_status = stat WHERE userId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}
#end pending

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

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertTeam(IN name VARCHAR(255), IN img LONGBLOB)
        BEGIN
            INSERT INTO vw_teams (teamName, image) VALUES (name, img);
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

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getContestant(IN id INT, IN evid VARCHAR(255))
        BEGIN
            SELECT * FROM vw_eventParti WHERE teamId = id AND eventId = evid;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventContestant(IN id VARCHAR(255))
        BEGIN
            SELECT * FROM vw_eventParti WHERE eventId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}


$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEventContestant(IN id INT, IN name VARCHAR(255), IN evid INT)
        BEGIN
            INSERT INTO vw_eventParti (teamId, teamName, eventId) VALUES (id, name, evid);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delEventContestant(IN id INT)
        BEGIN
            DELETE FROM vw_eventParti WHERE contId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventFaci(IN id INT)
        BEGIN
            SELECT * FROM vw_eventFaci WHERE eventId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getFaci(IN id VARCHAR(255), IN event INT)
        BEGIN
            SELECT * FROM vw_eventFaci WHERE faciId = id AND eventId = event;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEventFaci(IN id VARCHAR(255), 
        IN evid INT, IN evname VARCHAR(255), IN name VARCHAR(255))
        BEGIN
            INSERT INTO vw_eventFaci (faciId, eventId, eventName, faciName) 
            VALUES (id, evid, evname, name);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delEventFaci(IN id INT)
        BEGIN
            DELETE FROM vw_eventFaci WHERE faciNo = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventJudge(IN event INT)
        BEGIN
            SELECT * FROM vw_eventJudge WHERE eventId = event;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getJudge(IN id VARCHAR(255), IN event INT)
        BEGIN
            SELECT * FROM vw_eventJudge WHERE judgeId = id AND eventId = event;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAJudge(IN id VARCHAR(255))
        BEGIN
            SELECT * FROM vw_eventJudge WHERE judgeId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEventJudge(IN id VARCHAR(255), 
        IN evid INT, IN evname VARCHAR(255), IN name VARCHAR(255))
        BEGIN
            INSERT INTO vw_eventJudge VALUES (NULL, id, evid, evname, name);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delEventJudge(IN id INT)
        BEGIN
            DELETE FROM vw_eventJudge WHERE judgeNo = id;
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

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getScoringChk(IN num INT, IN catg VARCHAR(255))
        BEGIN
            SELECT * FROM vw_eventScore WHERE rankNo = num AND eventCategory = catg;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertScoring(IN num INT, IN rank VARCHAR(255), 
        IN category VARCHAR(255), IN pts INT)
        BEGIN
            INSERT INTO vw_eventScore VALUES (num, rank, category, pts);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delScoring(IN num INT)
        BEGIN
            DELETE FROM vw_eventScore WHERE rankNo = num;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getScoringDets(IN catg VARCHAR(255))
        BEGIN
            SELECT * FROM vw_eventScore WHERE eventCategory = catg;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}


$sqlP = "CREATE PROCEDURE IF NOT EXISTS sp_getCriteria(IN evid INT)
        BEGIN
            SELECT * FROM vw_criteria WHERE eventId = evid;
        END ;";
if ($conn->query($sqlP) === TRUE) {
} else {
    echo "Error: " . $conn->error;
}

$sqlP = "CREATE PROCEDURE IF NOT EXISTS sp_insertCriteria(IN evid INT, IN cri VARCHAR(255),
        IN pts INT)
        BEGIN
            INSERT INTO vw_criteria (eventId, criteria, percentage) 
            VALUES (evid, cri, pts);
        END ;";
if ($conn->query($sqlP) === TRUE) {
} else {
    echo "Error: " . $conn->error;
}

$sqlP = "CREATE PROCEDURE IF NOT EXISTS sp_editCriteria(IN id INT, IN cri VARCHAR(255),
        IN pts INT)
        BEGIN
            UPDATE vw_criteria SET criteria = cri, percentage = pts WHERE criteriaId = id;
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








#VIEWS (FACI) --------------------------------------------------------------------------------

$sqlF = "CREATE OR REPLACE VIEW vw_participants AS SELECT * FROM participants";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}




#TRIGGERS (FACI) --------------------------------------------------------------------------------

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


#PROCEDURES (FACI & JUDGE) ---------------------------------------------------------------------

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAFaci(IN id VARCHAR(255))
        BEGIN
            SELECT * FROM vw_eventFaci WHERE faciId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getPartiFrom(IN evid INT)
        BEGIN
            SELECT * FROM vw_eventParti WHERE eventId = evid;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getTeams(IN id INT)
        BEGIN
            SELECT * FROM vw_eventParti WHERE teamId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertResult(IN evid INT, IN team INT, IN perId VARCHAR(299),
        IN total DECIMAL(10,2), IN cri1 DECIMAL(10,2), IN cri2 DECIMAL(10,2), IN cri3 DECIMAL(10,2),
        IN cri4 DECIMAL(10,2), IN cri5 DECIMAL(10,2), IN cri6 DECIMAL(10,2), IN cri7 DECIMAL(10,2),
        IN cri8 DECIMAL(10,2), IN cri9 DECIMAL(10,2), IN cri10 DECIMAL(10,2))
        BEGIN
            INSERT INTO sub_results (
                eventId, contestantId, personnelId, total_score, criteria1, 
                criteria2, criteria3, criteria4, criteria5, criteria6, criteria7, 
                criteria8, criteria9, criteria10
            ) VALUES (
                evid, team, perId, total, cri1, cri2, cri3, cri4, cri5, cri6, 
                cri7, cri8, cri9, cri10
            );
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_updateContStat(IN team INT, IN evid INT)
        BEGIN
            UPDATE vw_eventParti SET status = 'finish' WHERE teamId = team AND eventId = evid;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertResultFaci(IN evid INT, IN team INT, IN perId VARCHAR(299),
        IN total DECIMAL(10,2))
        BEGIN
            INSERT INTO vw_subresult (eventId, contestantId, personnelId, total_score)
            VALUES (evid, team, perId, total);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_updateScore(IN total DECIMAL(10,2), IN perId VARCHAR(299),
        IN evid INT, IN conid INT)
        BEGIN
            UPDATE vw_subresult SET total_score = total, personnelId = perId WHERE eventId = evid AND
            contestantId = conid;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getScore(IN conid INT, IN evid INT)
        BEGIN
            SELECT * FROM vw_subresult WHERE contestantId = conid AND eventId = evid;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getScoreSport(IN evid INT)
        BEGIN
            SELECT c.teamName as name, sb.total_score as total_score from sub_results sb
            inner join contestant c on c.teamId = sb.contestantId
            where sb.eventId = evid GROUP BY sb.contestantId ORDER BY sb.total_score DESC;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_chckJudge(IN evid INT, IN perId VARCHAR(255))
        BEGIN
            SELECT contestant.teamId, contestant.teamName 
            FROM contestant 
            LEFT JOIN sub_results ON sub_results.contestantId = contestant.teamId
            WHERE sub_results.eventId = evid AND sub_results.personnelId = perId 
            GROUP BY contestant.teamId;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}






#VIEWS (JUDGE) --------------------------------------------------------------------------------

$sqlF = "CREATE OR REPLACE VIEW vw_subresult AS SELECT * FROM sub_results";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}


#PROCEDURES (JUDGE) --------------------------------------------------------------------------------------------------------

$conn->close();

?>
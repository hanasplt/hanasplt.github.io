<?php 

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Create database
require_once 'database.php';

// Database Created, tables and others will be created
require_once 'encryption.php';
$conn = require_once 'db.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sqlT = "CREATE TABLE IF NOT EXISTS teams (
    teamId INT AUTO_INCREMENT PRIMARY KEY,
    teamName VARCHAR(255) NOT NULL,
    members VARCHAR(255) NOT NULL,
    image LONGBLOB NOT NULL,
    status VARCHAR(255)
)";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS events (
    eventID INT AUTO_INCREMENT PRIMARY KEY,
    eventName VARCHAR(255) NOT NULL,
    eventType VARCHAR(255) NOT NULL,
    eventCategory VARCHAR(255) NOT NULL,
    eventElimination VARCHAR(255)
)";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS accounts (
    userId INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    firstName VARCHAR(255) NOT NULL,
    middleName VARCHAR(255),
    lastName VARCHAR(255) NOT NULL,
    suffix VARCHAR(50),
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    log_status VARCHAR(50),
    status INT,
    reset_token VARCHAR(64),
    reset_token_expiration DATETIME
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}


// Method to avoid admin account duplication and error
$adminW = "Admin";
$adminPass = encrypt("us3p@admin", $encryption_key);
$adminEmail = "hughlenecabanatan101@gmail.com";

try {
    $chkAdminAccExist = "SELECT * FROM accounts WHERE email = ?";
    $stmt = $conn->prepare($chkAdminAccExist);
    $stmt->bind_param("s", $adminEmail);
    $stmt->execute();
    $retval = $stmt->get_result();

    if($retval->num_rows > 0) {
        // Then don't insert admin's account
    } else {
        $stmt->close();

        // Insert Admin's Account
        $sqlInsertAdminAcc = "INSERT INTO vw_accounts 
                        VALUES 
                            (NULL, ?, ?, ?, NULL, ?, ?, ?, NULL, NULL, NULL)";
        $stmt = $conn->prepare($sqlInsertAdminAcc);
        $stmt->bind_param("ssssss", $adminW, $adminW, $adminW, $adminPass, $adminEmail, $adminW);
        if($stmt->execute()) {
            // Account added
        } else {
            echo 'Error: ' . $sql . "<br>" . $conn->error;
        }
        $stmt->close();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}


$sqlT = "CREATE TABLE IF NOT EXISTS contestant (
    contId INT AUTO_INCREMENT PRIMARY KEY,
    contNo INT,
    teamId INT NOT NULL,
    eventId INT NOT NULL,
    status VARCHAR(255), #finish when done judging
    FOREIGN KEY (teamId) REFERENCES teams(teamId) #new change
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS committee (
    comNo INT AUTO_INCREMENT PRIMARY KEY,
    comId INT NOT NULL,
    eventId INT NOT NULL,
    FOREIGN KEY (eventId) REFERENCES events(eventID), #new change
    FOREIGN KEY (comId) REFERENCES accounts(userId) #new change
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS judges (
    judgeNo INT AUTO_INCREMENT PRIMARY KEY,
    judgeId INT NOT NULL,
    eventId INT NOT NULL,
    FOREIGN KEY (judgeId) REFERENCES accounts(userId), #new change
    FOREIGN KEY (eventId) REFERENCES events(eventID) #new change
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

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

$sqlT = "CREATE TABLE IF NOT EXISTS criteria (
    criteriaId INT AUTO_INCREMENT PRIMARY KEY,
    eventId INT NOT NULL,
    criteria VARCHAR(255) NOT NULL,
    percentage INT NOT NULL,
    FOREIGN KEY (eventId) REFERENCES events(eventID) #new change
    );";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE TABLE IF NOT EXISTS sub_results (
    subId INT AUTO_INCREMENT PRIMARY KEY,
    eventId INT NOT NULL,
    contestantId INT NOT NULL,
    personnelId INT NOT NULL,
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
    criteria10 DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (eventId) REFERENCES events(eventID), #new change
    FOREIGN KEY (contestantId) REFERENCES contestant(contId), #new change
    FOREIGN KEY (personnelId) REFERENCES accounts(userId) #new change
)";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}


// Table for access log or audit log
$sqlT = "CREATE TABLE IF NOT EXISTS adminlogs (
    logId INT AUTO_INCREMENT PRIMARY KEY,
    date_on DATETIME NOT NULL,
    userId INT NOT NULL, #to be implemented
    actions VARCHAR(255) NOT NULL,
    FOREIGN KEY (userId) REFERENCES accounts(userId) #to be implemented
    );";

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

$sqlF = "CREATE OR REPLACE VIEW vw_accounts AS SELECT * FROM accounts";
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

$sqlF = "CREATE OR REPLACE VIEW vw_eventComt AS SELECT * FROM committee";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_eventJudge AS SELECT * FROM judges";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_eventScore AS SELECT * FROM eventScoring";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlF = "CREATE OR REPLACE VIEW vw_logs AS SELECT * FROM adminlogs";

if ($conn->query($sqlF) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}



#PROCEDURES (ADMIN ALL) --------------------------------------------------------------------------------------------------------

#newwwww
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delLead()
        BEGIN
            DROP TABLE IF EXISTS leaderboard;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getTeamData(IN evid INT)
        BEGIN
            select eventId, contestantId, (select vw_teams.teamName from vw_eventParti 
            INNER JOIN vw_teams on vw_eventParti.teamId = vw_teams.teamId 
            where vw_eventParti.contId = vw_subresult.contestantId
            and eventId = vw_subresult.eventId) as team, sum(total_score) as score, 
            RANK() OVER (ORDER BY SUM(total_score) DESC)
            as rank from vw_subresult where eventId = evid group by contestantId order by rank ASC;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getLead()
        BEGIN
            SELECT RANK() OVER (ORDER BY SUM(points) DESC) AS rank, t.teamName as team, SUM(l.points) 
            AS pts FROM leaderboard l INNER JOIN vw_eventParti p ON l.conId = p.contId 
            INNER JOIN vw_teams t ON t.teamId = p.teamId
            GROUP BY team ORDER BY rank ASC;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getData(IN evid INT)
        BEGIN
            select eventId, (select vw_teams.teamName from vw_eventParti 
            INNER JOIN vw_teams on vw_eventParti.teamId = vw_teams.teamId 
            where vw_eventParti.contId = vw_subresult.contestantId and 
            eventId = vw_subresult.eventId) as team, sum(total_score) as score, 
            RANK() OVER (ORDER BY SUM(total_score) DESC) AS rank from vw_subresult where 
            eventId = evid group by contestantId order by rank ASC;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getRanking(IN rank INT, IN evid INT)
        BEGIN
            select * from vw_eventscore where rankNo = rank and 
            eventCategory = (select eventCategory from vw_events where eventId = evid);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}
#end new


// NAGAMIT
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAllAcc()
        BEGIN
            SELECT * FROM accounts WHERE status IS NULL;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAnAcc(IN id INT)
        BEGIN
            SELECT * FROM vw_accounts WHERE userId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}


// NAGAMIT
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAcc(IN limit_num INT)
        BEGIN
            SELECT * FROM vw_accounts WHERE status IS NULL LIMIT limit_num;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}


// NAGAMIT
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertAcc(IN fn VARCHAR(255),
        IN mn VARCHAR(255), IN ln VARCHAR(255), IN sfx VARCHAR(50), IN em VARCHAR(255), IN pass VARCHAR(255),
        IN typ VARCHAR(50))
        BEGIN
            INSERT INTO vw_accounts (firstName, middleName, lastName, suffix, email, password, type) 
            VALUES (fn, mn, ln, sfx, em, pass, typ);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}


// NAGAMIT
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_editAcc(IN id INT, IN fn VARCHAR(255),
        IN mn VARCHAR(255), IN ln VARCHAR(255), IN sfx VARCHAR(50), IN em VARCHAR(255), IN pass VARCHAR(255),
        IN acctype VARCHAR(50))
        BEGIN
            UPDATE vw_accounts SET firstName = fn, middleName = mn, lastName = ln, suffix = sfx, 
            email = em, password = pass, type = acctype WHERE userId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_editAccPass(IN id INT, IN pass VARCHAR(255),
        IN stat VARCHAR(50))
        BEGIN
            UPDATE vw_accounts SET password = pass, log_status = stat WHERE userId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_resetAccPass(IN id INT, IN pass VARCHAR(255),
        IN token VARCHAR(64))
        BEGIN
            UPDATE vw_accounts SET password = pass, reset_token = token,
            reset_token_expiration = token WHERE userId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

// NAGAMIT
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delAcc(IN id VARCHAR(255))
        BEGIN
            UPDATE vw_accounts SET status = 0 WHERE userId = id;
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


// NAGAMIT
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getTeam(IN limit_num INT, IN offset INT)
        BEGIN
            SELECT * FROM vw_teams WHERE status IS NULL LIMIT limit_num OFFSET offset;
        END ;";

if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}


// NAGAMIT
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getTeamCount()
        BEGIN
            SELECT COUNT(*) AS total FROM vw_teams WHERE status IS NULL;
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


// NAGAMIT
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertTeam(IN name VARCHAR(255), IN mem VARCHAR(255), IN img LONGBLOB)
        BEGIN
            INSERT INTO vw_teams (teamName, members, image) VALUES (name, mem, img);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}


// NAGAMIT
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_editTeam(IN id INT, IN name VARCHAR(255), IN mem VARCHAR(255), IN img BLOB)
        BEGIN
            UPDATE vw_teams SET teamName = name, members = mem, image = img WHERE teamId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}


// NAGAMIT
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_editTeamName(IN id INT, IN name VARCHAR(255), IN mem VARCHAR(255))
        BEGIN
            UPDATE vw_teams SET teamName = name, members = mem WHERE teamId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}


// NAGAMIT
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delTeam(IN id INT)
        BEGIN
            UPDATE vw_teams SET status = 0 WHERE teamId = id;
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


// NAGAMIT
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEvent(IN id INT, IN name VARCHAR(255), 
        IN evtype VARCHAR(255), IN category VARCHAR(255), IN elim VARCHAR(255))
        BEGIN
            INSERT INTO vw_events VALUES (id, name, evtype, category, elim);
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

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_editEvent(IN id INT, IN type VARCHAR(255), IN name VARCHAR(255), 
        IN category VARCHAR(255))
        BEGIN
            UPDATE vw_events SET eventType = type, eventName = name, eventCategory = category 
            WHERE eventID = id;
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
            SELECT contId, contNo, vw_eventParti.teamId, vw_teams.teamName as team FROM vw_eventParti 
            INNER JOIN vw_teams on vw_eventParti.teamId = vw_teams.teamId WHERE eventId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventContestantJ(IN id VARCHAR(255))
        BEGIN
            SELECT contId, vw_eventParti.teamId, vw_teams.teamName as team FROM vw_eventParti 
            INNER JOIN vw_teams on vw_eventParti.teamId = vw_teams.teamId WHERE eventId = id AND
            status IS NULL;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}


$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEventContestant(IN id INT, IN evid INT)
        BEGIN
            INSERT INTO vw_eventParti (teamId, eventId) VALUES (id, evid);
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

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventComt(IN id INT)
        BEGIN
            SELECT f.*, a.firstName FROM vw_eventComt f INNER JOIN vw_accounts a ON f.comId = a.userId 
            WHERE eventId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getComt(IN id INT, IN event INT)
        BEGIN
            SELECT * FROM vw_eventComt WHERE comId = id AND eventId = event;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEventComt(IN id INT, 
        IN evid INT)
        BEGIN
            INSERT INTO vw_eventComt (comId, eventId) 
            VALUES (id, evid);
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delEventComt(IN id INT)
        BEGIN
            DELETE FROM vw_eventComt WHERE comNo = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventJudge(IN event INT)
        BEGIN
            SELECT j.*, a.firstName FROM vw_eventJudge j INNER JOIN vw_accounts a ON j.judgeId = a.userId
            WHERE eventId = event;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getJudge(IN id INT, IN event INT)
        BEGIN
            SELECT j.*, a.firstName FROM vw_eventJudge j 
            INNER JOIN vw_accounts a ON j.judgeId = a.userId WHERE judgeId = id AND eventId = event;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAJudge(IN id INT)
        BEGIN
            SELECT j.*, ev.eventName FROM vw_eventJudge j INNER JOIN vw_events ev ON j.eventId = ev.eventID 
            WHERE judgeId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEventJudge(IN id INT, 
        IN evid INT)
        BEGIN
            INSERT INTO vw_eventJudge VALUES (NULL, id, evid);
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



#PROCEDURES (COMMITTEE & JUDGE) ---------------------------------------------------------------------

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAComt(IN id INT)
        BEGIN
            SELECT ef.eventId, ev.eventName FROM vw_eventComt ef 
            INNER JOIN vw_events ev ON ef.eventId = ev.eventID WHERE comId = id;
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
            SELECT p.*, t.teamName FROM vw_eventParti p 
            INNER JOIN vw_teams t ON p.teamId = t.teamId WHERE p.contId = id;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertResult(IN evid INT, IN team INT, IN perId VARCHAR(255),
        IN total DECIMAL(10,2), IN cri1 DECIMAL(10,2), IN cri2 DECIMAL(10,2), IN cri3 DECIMAL(10,2),
        IN cri4 DECIMAL(10,2), IN cri5 DECIMAL(10,2), IN cri6 DECIMAL(10,2), IN cri7 DECIMAL(10,2),
        IN cri8 DECIMAL(10,2), IN cri9 DECIMAL(10,2), IN cri10 DECIMAL(10,2))
        BEGIN
            INSERT INTO vw_subresult (
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

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_updateContStat(IN parti INT)
        BEGIN
            UPDATE vw_eventParti SET status = 'finish' WHERE contId = parti;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertResultComt(IN evid INT, IN team INT, IN perId INT,
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

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getScoreJudge(IN evid INT, IN evid1 INT, IN perid INT)
        BEGIN
            select *, (select DISTINCT t.teamName from vw_eventParti p 
            inner join vw_teams t on p.teamId = t.teamId
            where eventId = evid and p.contId = contestantId) as teamname from vw_subresult 
            where eventId = evid1 AND personnelId = perid;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getJudges(IN evid INT)
        BEGIN
            select DISTINCT (SELECT userId from vw_accounts where userId = personnelId) 
            AS perId, 
            (SELECT firstName from vw_accounts where userId = personnelId) AS perName 
            from vw_subresult where eventId = evid;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getScoreSport(IN evid INT)
        BEGIN
            SELECT 
                t.teamName as name, 
                sb.total_score as total_score,
                DENSE_RANK() OVER (ORDER BY sb.total_score DESC) as rank
            FROM 
                vw_subresult sb
            INNER JOIN 
                vw_eventParti c on c.contId = sb.contestantId
            INNER JOIN 
                vw_teams t on c.teamId = t.teamId
            WHERE 
                sb.eventId = evid
            GROUP BY 
                sb.contestantId, t.teamName, sb.total_score
            ORDER BY 
                sb.total_score DESC;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_chckJudge(IN evid INT, IN perId VARCHAR(255))
        BEGIN
            SELECT vw_eventParti.teamId, vw_teams.teamName 
            FROM vw_eventParti 
            INNER JOIN vw_teams on vw_eventParti.teamId = vw_teams.teamId
            LEFT JOIN vw_subresult ON vw_subresult.contestantId = vw_eventParti.teamId
            WHERE vw_subresult.eventId = evid AND vw_subresult.personnelId = perId 
            GROUP BY vw_eventParti.teamId;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}



// NAGAMIT
// Stored Procedure for displaying logs
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_displayLog()
        BEGIN
            SELECT vl.*, CONCAT(va.firstName, ' ', va.lastName) AS fullname
            FROM vw_logs vl
            INNER JOIN vw_accounts va ON vl.userId = va.userId;
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









#ADD DAY (SCHEDULE) --------------------------------------------------------------------------------

$sqlDAYS = "CREATE TABLE IF NOT EXISTS scheduled_days (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day_date DATE NOT NULL
    );";

if ($conn->query($sqlDAYS) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

#ADD EVENT TO DAY (SCHEDULE) --------------------------------------------------------------------------------

$sqlEvDay = "CREATE TABLE IF NOT EXISTS scheduled_eventstoday (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day_id INT,
    time TIME NOT NULL,
    activity VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    status ENUM('Pending', 'Ongoing', 'Ended', 'Cancelled', 'Moved') DEFAULT 'Pending',
    FOREIGN KEY (day_id) REFERENCES scheduled_days(id) ON DELETE CASCADE
    );";
    #if the day is deleted, all the events linked to the day is also deleted

if ($conn->query($sqlEvDay) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();

?>
<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Create database
require_once 'database.php';

// Database Created, tables and others will be created
require_once 'encryption.php';
require_once 'db.php';

/* Teams Table */
try {
    $sqlT = "CREATE TABLE IF NOT EXISTS teams (
        teamId INT AUTO_INCREMENT PRIMARY KEY,
        teamName VARCHAR(255) NOT NULL,
        members VARCHAR(255) NOT NULL,
        image LONGBLOB NOT NULL
    )";

    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
/* Events Table */
try {
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
} catch (Exception $e) {
    echo $e->getMessage();
}
/* Accounts Table */
try {
    $sqlT = "CREATE TABLE IF NOT EXISTS accounts (
        userId INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
        firstName VARCHAR(255) NOT NULL,
        middleName VARCHAR(255),
        lastName VARCHAR(255) NOT NULL,
        suffix VARCHAR(50),
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        type VARCHAR(50) NOT NULL,
        permissions VARCHAR(599) NOT NULL,
        log_status VARCHAR(50),
        reset_token VARCHAR(64),
        reset_token_expiration DATETIME
        );";

    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}


/* Admin Data for insertion */
$adminW = "Admin";
$adminPass = encrypt("us3p@admin", $encryption_key);
$adminEmail = "ilps.usep@gmail.com";
$user_permissions = "user_read,user_add,user_update,user_delete,role_read,role_update,team_read,team_add,team_update,team_delete,event_read,event_add,event_update,event_delete,contestant_read,contestant_add,contestant_delete,committee_read,committee_add,committee_delete,judge_read,judge_add,judge_delete,criteria_read,criteria_add,criteria_update,criteria_delete,scoring_read,scoring_add,scoring_delete,schedule_read,schedule_add,schedule_update,schedule_delete,scheduledEvent_read,scheduledEvent_add,scheduledEvent_update,scheduledEvent_delete,reports_read,logs_read";

/* Built-in Admin Insertion */
try {
    // Method to avoid admin account duplication and error
    $chkAdminAccExist = "SELECT * FROM accounts WHERE email = ?";
    $stmt = $conn->prepare($chkAdminAccExist);
    $stmt->bind_param("s", $adminEmail);
    $stmt->execute();
    $retval = $stmt->get_result();

    if ($retval->num_rows > 0) {
        // Then don't insert admin's account
    } else {
        $stmt->close();

        // Insert Admin's Account
        $sqlInsertAdminAcc = "INSERT INTO accounts 
                        VALUES 
                            (NULL, ?, ?, ?, NULL, ?, ?, ?, ?, NULL, NULL, NULL)";
        $stmt = $conn->prepare($sqlInsertAdminAcc);
        $stmt->bind_param(
            "sssssss",
            $adminW,
            $adminW,
            $adminW,
            $adminPass,
            $adminEmail,
            $adminW,
            $user_permissions
        );

        if ($stmt->execute()) {
            // Account added
        } else {
            echo 'Error: ' . $sql . "<br>" . $conn->error;
        }
        $stmt->close();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
/* Contestant Table */
try {
    $sqlT = "CREATE TABLE IF NOT EXISTS contestant (
        contId INT AUTO_INCREMENT PRIMARY KEY,
        contNo INT,
        teamId INT NOT NULL,
        eventId INT NOT NULL,
        FOREIGN KEY (teamId) REFERENCES teams(teamId) ON DELETE CASCADE
        );";

    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
/* Committee Table */
try {
    $sqlT = "CREATE TABLE IF NOT EXISTS committee (
        comNo INT AUTO_INCREMENT PRIMARY KEY,
        comId INT NOT NULL,
        eventId INT NOT NULL,
        FOREIGN KEY (eventId) REFERENCES events(eventID) ON DELETE CASCADE,
        FOREIGN KEY (comId) REFERENCES accounts(userId) ON DELETE CASCADE
        );";

    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
/* Judges Table */
try {
    $sqlT = "CREATE TABLE IF NOT EXISTS judges (
        judgeNo INT AUTO_INCREMENT PRIMARY KEY,
        judgeId INT NOT NULL,
        eventId INT NOT NULL,
        FOREIGN KEY (judgeId) REFERENCES accounts(userId) ON DELETE CASCADE,
        FOREIGN KEY (eventId) REFERENCES events(eventID) ON DELETE CASCADE
        );";

    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

/* Scoring Table */
try {
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
} catch (Exception $e) {
    echo $e->getMessage();
}
/* Criteria Table */
try {
    $sqlT = "CREATE TABLE IF NOT EXISTS criteria (
        criteriaId INT AUTO_INCREMENT PRIMARY KEY,
        eventId INT NOT NULL,
        criteria VARCHAR(255) NOT NULL,
        percentage INT NOT NULL,
        FOREIGN KEY (eventId) REFERENCES events(eventID) ON DELETE CASCADE
        );";

    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
/* Result Table (Score from Committee and Judges) */
try {
    $sqlT = "CREATE TABLE IF NOT EXISTS sub_results (
        subId INT AUTO_INCREMENT PRIMARY KEY,
        eventId INT NOT NULL,
        contestantId INT NOT NULL,
        personnelId INT NOT NULL,
        total_score INT NOT NULL,
        criteria1 INT NOT NULL,
        criteria2 INT NOT NULL,
        criteria3 INT NOT NULL,
        criteria4 INT NOT NULL,
        criteria5 INT NOT NULL,
        criteria6 INT NOT NULL,
        criteria7 INT NOT NULL,
        criteria8 INT NOT NULL,
        criteria9 INT NOT NULL,
        criteria10 INT NOT NULL,
        FOREIGN KEY (eventId) REFERENCES events(eventID) ON DELETE CASCADE, 
        FOREIGN KEY (contestantId) REFERENCES contestant(contId) ON DELETE CASCADE, 
        FOREIGN KEY (personnelId) REFERENCES accounts(userId) ON DELETE CASCADE 
    )";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

/* Table for access log or audit log */
try {
    $sqlT = "CREATE TABLE IF NOT EXISTS adminlogs (
        logId INT AUTO_INCREMENT PRIMARY KEY,
        date_on DATETIME NOT NULL,
        userId INT NOT NULL,
        actions VARCHAR(255) NOT NULL,
        FOREIGN KEY (userId) REFERENCES accounts(userId) ON DELETE CASCADE
        );";

    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

/* Tables for scheduling */
/* (1) For adding days */
try {
    $sqlDAYS = "CREATE TABLE IF NOT EXISTS scheduled_days (
        id INT AUTO_INCREMENT PRIMARY KEY,
        day_date DATE NOT NULL
        );";

    if ($conn->query($sqlDAYS) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
/* (2) For adding events */
try {
    $sqlEvDay = "CREATE TABLE IF NOT EXISTS scheduled_eventstoday ( #schedule event/activity table
        id INT AUTO_INCREMENT PRIMARY KEY,
        day_id INT,
        time TIME NOT NULL,
        type ENUM('Sports', 'Socio-Cultural', 'Others') NOT NULL,
        activity VARCHAR(255) NOT NULL,
        gameNo INT,
        teamA INT,
        teamB INT,
        location VARCHAR(255) NOT NULL,
        status ENUM('Pending', 'Ongoing', 'Ended', 'Cancelled', 'Moved') DEFAULT 'Pending',
        ResultA VARCHAR(255) NOT NULL,
        ResultB VARCHAR(255) NOT NULL,
        FOREIGN KEY (day_id) REFERENCES scheduled_days(id) ON DELETE CASCADE,
        FOREIGN KEY (teamA) REFERENCES contestant(teamId) ON DELETE CASCADE,
        FOREIGN KEY (teamB) REFERENCES contestant(teamId) ON DELETE CASCADE
        );";
    #if the day is deleted, all the events linked to the day is also deleted

    if ($conn->query($sqlEvDay) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}


#VIEWS (ADMIN ALL) --------------------------------------------------------------------------------------------------------

try {
    $sqlF = "CREATE OR REPLACE VIEW vw_sched AS SELECT * FROM scheduled_days;";
    if ($conn->query($sqlF) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

    $sqlF = "CREATE OR REPLACE VIEW vw_eventSched AS SELECT * FROM scheduled_eventstoday;";
    if ($conn->query($sqlF) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

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

    $sqlF = "CREATE OR REPLACE VIEW vw_subresult AS SELECT * FROM sub_results";

    if ($conn->query($sqlF) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

#STORED PROCEDURES (ALL) --------------------------------------------------------------------------------------------------------
/* RESET & FORGOT PASSWORD */
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_UpdateToken(IN token VARCHAR(255), IN eml VARCHAR(255))
            BEGIN
                UPDATE accounts
                SET reset_token = token,
                    reset_token_expiration = NOW() + INTERVAL 10 MINUTE
                WHERE email = eml;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_resetPass(IN eml VARCHAR(255))
            BEGIN
                SELECT userId
                FROM accounts
                WHERE email = eml;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
/* END RESET & FORGOT PASSWORD */


/* PENDING (@NINO) Leaderboard ni, nagamit yata nako ni sa spectator */
try {
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
} catch (Exception $e) {
    echo $e->getMessage();
}

/* RANK */
try {
    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getData(IN evid INT)
            BEGIN
            SELECT 
                    vs.eventId, 
                    vt.teamName as team,
                    sum(vs.total_score) AS score, 
                    RANK() OVER (ORDER BY SUM(vs.total_score) DESC) AS rank 
                FROM vw_subresult vs
                INNER JOIN vw_eventparti vp ON vs.contestantId = vp.contId
                INNER JOIN vw_teams vt ON vp.teamId = vt.teamId
                WHERE vs.eventId = evid GROUP BY vs.contestantId ORDER BY rank ASC;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getRanking(IN rank INT, IN evid INT)
            BEGIN
                SELECT * FROM vw_eventscore 
                WHERE 
                    rankNo = rank AND 
                    eventCategory = (SELECT eventCategory FROM vw_events WHERE eventId = evid);
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

/* ACCOUNTS */
try {
    //NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAccount(
            IN search_query VARCHAR(255), 
            IN limit_num INT, 
            IN offset_num INT,
            IN id INT)
        BEGIN
            SELECT * FROM vw_accounts
            WHERE 
                userId != 1 AND userId != id AND 
                (CONCAT(firstName, ' ', lastName) LIKE search_query
                OR CONCAT(firstName, ' ', middleName, ' ', lastName) LIKE search_query)
            LIMIT limit_num OFFSET offset_num;
        END ;";

    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating procedure: " . $conn->error;
    }

    //NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAccountCount(
            IN search_query VARCHAR(255), IN id INT)
        BEGIN
            SELECT COUNT(*) AS total FROM vw_accounts
            WHERE 
                userId != 1 AND userId != id AND 
                (CONCAT(firstName, ' ', lastName) LIKE search_query OR 
                CONCAT(firstName, ' ', middleName, ' ', lastName) LIKE search_query);
        END ;";

    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating procedure: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAllAcc()
            BEGIN
                SELECT * FROM vw_accounts;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT 
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAnAcc(IN id INT)
            BEGIN
                SELECT * FROM vw_accounts WHERE userId = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAcc(IN id INT, IN limit_num INT)
            BEGIN
                SELECT * FROM vw_accounts 
                WHERE 
                    userId != 1 AND userId != id 
                LIMIT limit_num;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertAcc(
                IN fn VARCHAR(255), IN mn VARCHAR(255), 
                IN ln VARCHAR(255), IN sfx VARCHAR(50), 
                IN em VARCHAR(255), IN pass VARCHAR(255),
                IN typ VARCHAR(50), IN rights VARCHAR(599)
            )
            BEGIN
                INSERT INTO vw_accounts 
                    (firstName, middleName, lastName, suffix, email, password, type, permissions) 
                VALUES (fn, mn, ln, sfx, em, pass, typ, rights);
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_editAcc(
                IN id INT, IN fn VARCHAR(255), IN mn VARCHAR(255), 
                IN ln VARCHAR(255), IN sfx VARCHAR(50), IN em VARCHAR(255), 
                IN pass VARCHAR(255), IN acctype VARCHAR(50), IN rights VARCHAR(599)
            )
            BEGIN
                UPDATE vw_accounts SET firstName = fn, middleName = mn, lastName = ln, suffix = sfx, 
                email = em, password = pass, type = acctype, permissions = rights WHERE userId = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_editAccPass(IN id INT, IN pass VARCHAR(255),
            IN stat VARCHAR(50))
            BEGIN
                UPDATE vw_accounts SET password = pass, log_status = stat WHERE userId = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // IDK
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

    // NAGAMIT (MODIFIED)
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delAcc(IN id INT)
            BEGIN
                DELETE FROM vw_accounts WHERE userId = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAccType(IN ptype VARCHAR(255))
            BEGIN
                SELECT * FROM vw_accounts WHERE type = ptype;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // IDK
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAdmin()
            BEGIN
                SELECT * FROM vw_admin;
            END ;";

    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delPrevRole(IN thisTable VARCHAR(50), IN id INT)
            BEGIN
                IF thisTable = 'vw_eventJudge' THEN
                    DELETE FROM vw_eventJudge WHERE judgeId = id;
                ELSEIF thisTable = 'vw_eventComt' THEN
                    DELETE FROM vw_eventComt WHERE comId = id;
                END IF;
            END;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
/* END ACCOUNTS */

/* TEAMS */
try {
    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getTeam(IN limit_num INT, IN offset INT)
            BEGIN
                SELECT * FROM vw_teams LIMIT limit_num OFFSET offset;
            END ;";

    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getATeam(IN id INT)
            BEGIN
                SELECT * FROM vw_teams WHERE teamId = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertTeam(
                IN name VARCHAR(255), IN mem VARCHAR(255), IN img LONGBLOB
            )
            BEGIN
                INSERT INTO vw_teams (teamName, members, image) VALUES (name, mem, img);
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_editTeam(
                IN id INT, IN name VARCHAR(255), IN mem VARCHAR(255), IN img BLOB
            )
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
                DELETE FROM vw_teams WHERE teamId = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAllTeam()
            BEGIN
                SELECT * FROM vw_teams;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
    
    
    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getTeamInEvent(IN event_id INT)
            BEGIN
                SELECT t.teamId, t.teamName
                FROM vw_teams t
                INNER JOIN vw_eventparti c ON t.teamId = c.teamId
                INNER JOIN vw_events e ON c.eventId = e.eventId
                INNER JOIN vw_eventsched s ON e.eventName = s.activity
                WHERE s.id = event_id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
/* END TEAMS */

/* EVENTS */
try {
    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEvents()
            BEGIN
                SELECT * FROM vw_events;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventFrom(IN type VARCHAR(255))
            BEGIN
                SELECT * FROM vw_events WHERE eventType = type;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // IDK
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEvent(IN id INT)
            BEGIN
                SELECT * FROM vw_events WHERE eventID = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
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
            IN evtype VARCHAR(255), IN category VARCHAR(255))
            BEGIN
                INSERT INTO vw_events VALUES (id, name, evtype, category);
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delEvent(IN id INT)
            BEGIN
                DELETE FROM vw_events WHERE eventID = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_editEvent(IN id INT, IN type VARCHAR(255), IN name VARCHAR(255), 
            IN category VARCHAR(255))
            BEGIN
                UPDATE vw_events 
                SET eventType = type, 
                    eventName = name, 
                    eventCategory = category 
                WHERE eventID = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getContestant(IN id INT, IN evid INT)
            BEGIN
                SELECT * FROM vw_eventParti WHERE teamId = id AND eventID = evid;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventContestant(IN id INT)
            BEGIN
                SELECT 
                    vp.contId, vp.contNo, vp.teamId, vt.teamName as team
                FROM vw_eventParti vp 
                INNER JOIN vw_teams vt on vp.teamId = vt.teamId
                INNER JOIN vw_events ve on vp.eventId = ve.eventID
                WHERE vp.eventId = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // IDK
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventContestantJ(IN id VARCHAR(255))
            BEGIN
                SELECT contId, vw_eventParti.teamId, vw_teams.teamName as team FROM vw_eventParti 
                INNER JOIN vw_teams on vw_eventParti.teamId = vw_teams.teamId WHERE eventId = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEventContestant(IN id INT, IN evid INT, IN num INT)
            BEGIN
                INSERT INTO vw_eventParti (contNo, teamId, eventId) VALUES (num, id, evid);
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // IDK
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delEventContestant(IN id INT)
            BEGIN
                DELETE FROM vw_eventParti WHERE contId = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventComt(IN id INT)
            BEGIN
                SELECT f.*, a.firstName, a.lastName 
                FROM vw_eventComt f 
                INNER JOIN vw_accounts a ON f.comId = a.userId 
                WHERE eventId = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getComt(IN id INT, IN event INT)
            BEGIN
                SELECT * FROM vw_eventComt WHERE comId = id AND eventId = event;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
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


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delEventComt(IN id INT)
            BEGIN
                DELETE FROM vw_eventComt WHERE comNo = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getEventJudge(IN event INT)
            BEGIN
                SELECT j.*, a.firstName, a.lastName 
                FROM vw_eventJudge j 
                INNER JOIN vw_accounts a ON j.judgeId = a.userId
                WHERE eventId = event;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getJudge(IN id INT, IN event INT)
            BEGIN
                SELECT j.*, a.firstName FROM vw_eventJudge j 
                INNER JOIN vw_accounts a ON j.judgeId = a.userId 
                WHERE judgeId = id AND eventId = event;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAJudge(IN id INT)
            BEGIN
                SELECT j.*, ev.eventName FROM vw_eventJudge j 
                INNER JOIN vw_events ev ON j.eventId = ev.eventID 
                WHERE judgeId = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEventJudge(IN id INT, IN evid INT)
            BEGIN
                INSERT INTO vw_eventJudge VALUES (NULL, id, evid);
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delEventJudge(IN id INT)
            BEGIN
                DELETE FROM vw_eventJudge WHERE judgeNo = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
/* END EVENTS */

/* SCORING */
try {
    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getScoring()
            BEGIN
                SELECT * FROM vw_eventScore;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getScoringChk(IN num INT)
            BEGIN
                SELECT * FROM vw_eventScore WHERE rankNo = num;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertScoring(IN num INT, IN rank VARCHAR(255), 
            IN category VARCHAR(255), IN pts INT)
            BEGIN
                INSERT INTO vw_eventScore VALUES (num, rank, category, pts);
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }


    // NAGAMIT
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
} catch (Exception $e) {
    echo $e->getMessage();
}
/* END SCORING */

/* CRITERIA */
try {
    // NAGAMIT
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


    // NAGAMIT
    $sqlP = "CREATE PROCEDURE IF NOT EXISTS sp_editCriteria(IN id INT, IN cri VARCHAR(255),
            IN pts INT, IN evid INT)
            BEGIN
                UPDATE vw_criteria SET eventId = evid, criteria = cri, percentage = pts 
                WHERE criteriaId = id;
            END ;";
    if ($conn->query($sqlP) === TRUE) {
    } else {
        echo "Error: " . $conn->error;
    }


    // NAGAMIT
    $sqlP = "CREATE PROCEDURE IF NOT EXISTS sp_delCriteria(IN id INT)
            BEGIN
                DELETE FROM vw_criteria WHERE eventID = id;
            END ;";
    if ($conn->query($sqlP) === TRUE) {
    } else {
        echo "Error: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
/* END CRITERIA */

/* SCHEDULE */

// Retrieving scheduled day
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getDay()
            BEGIN
                SELECT * FROM vw_sched;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

// Retrieving scheduled event by day_id
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getScheduledEvent(IN id INT)
            BEGIN
                SELECT * FROM vw_eventSched WHERE day_id = id ORDER BY time ASC;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

// Retrieving scheduled event by id
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_checkScheduledEvent(IN check_id INT)
            BEGIN
                SELECT * FROM vw_eventSched WHERE id = check_id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}


try {
    // Inserting day on schedule
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertDay(IN daydate DATE)
            BEGIN
                INSERT INTO vw_sched (day_date) VALUES (daydate);
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

// Updating day on schedule
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_updateDay(IN daydate DATE, IN dayId INT)
            BEGIN
                UPDATE vw_sched SET day_date = daydate WHERE id = dayId;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

// Removing day on schedule
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delDay(IN dayId INT)
            BEGIN
                DELETE FROM vw_sched WHERE id = dayId;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}


// Retrieving existing date in the database
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getDate(IN date VARCHAR(50))
            BEGIN
                SELECT COUNT(*) AS dateCount FROM vw_sched WHERE day_date = date;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}


// Inserting event on scheduled day
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEventSched(
                IN id INT, 
                IN time24 VARCHAR(50), 
                IN evtype VARCHAR(299), 
                IN evactivity VARCHAR(255), 
                IN evlocation VARCHAR(255), 
                IN gameNo24 INT, 
                IN teamA24 INT, 
                IN teamB24 INT, 
                IN evstatus VARCHAR(255)
            )
            BEGIN
                INSERT INTO vw_eventSched 
                    (day_id, time, type, activity, location, gameNo, teamA, teamB, status) 
                VALUES 
                    (id, time24, evtype, evactivity, evlocation, gameNo24, teamA24, teamB24, evstatus);
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}


// Update event on scheduled day
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_updateEventSched(
                IN time24 VARCHAR(50), 
                IN evtype VARCHAR(299), 
                IN evactivity VARCHAR(255), 
                IN evlocation VARCHAR(255), 
                IN gameNo24 INT, 
                IN teamA24 INT, 
                IN teamB24 INT, 
                IN evstatus VARCHAR(255),
                IN schedId INT
            )
            BEGIN
                UPDATE vw_eventSched 
                SET 
                    time = time24, type = evtype, activity = evactivity, location = evlocation, 
                    gameNo = gameNo24, teamA = teamA24, teamB = teamB24, status = evstatus 
                WHERE id = schedId;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}


// Remove event on scheduled day
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_delEventSched(IN schedId INT)
            BEGIN
                DELETE FROM vw_eventSched WHERE id = schedId;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}


// Retrieve team for dropdown
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertEventSched(
                IN id INT, 
                IN time24 VARCHAR(50), 
                IN evtype VARCHAR(299), 
                IN evactivity VARCHAR(255), 
                IN evlocation VARCHAR(255), 
                IN gameNo24 INT, 
                IN teamA24 INT, 
                IN teamB24 INT, 
                IN evstatus VARCHAR(255)
            )
            BEGIN
                INSERT INTO vw_eventSched 
                    (day_id, time, type, activity, location, gameNo, teamA, teamB, status) 
                VALUES 
                    (id, time24, evtype, evactivity, evlocation, gameNo24, teamA24, teamB24, evstatus);
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
/* END SCHEDULE */




/* LOGS */
// Stored Procedure for displaying logs
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_displayLog()
            BEGIN
                SELECT vl.*, CONCAT(va.firstName, ' ', va.lastName) AS fullname
                FROM vw_logs vl
                INNER JOIN vw_accounts va ON vl.userId = va.userId
                ORDER BY vl.logId;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

// inserting log NAGAMIT
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_insertLog(IN id INT, IN act VARCHAR(255))
            BEGIN
                INSERT INTO vw_logs VALUES (NULL, NOW(), id, act);
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}


// getting logs NAGAMIT
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getLogs(IN inpYear INT, IN inpLimit INT, IN offset INT)
            BEGIN
                SELECT al.logId, al.date_on, 
                    CONCAT(a.firstName, ' ', 
                           a.lastName) AS fullname, 
                    al.actions 
                FROM vw_logs al 
                JOIN vw_accounts a ON al.userId = a.userId
                WHERE YEAR(al.date_on) = inpYear
                ORDER BY al.logId
                LIMIT inpLimit OFFSET offset;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
/* END LOGS */



#PROCEDURES (COMMITTEE & JUDGE) ---------------------------------------------------------------------
/* COMMITTEE */
try {
    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getAComt(IN id INT)
            BEGIN
                SELECT ef.eventId, ev.eventName FROM vw_eventComt ef 
                INNER JOIN vw_events ev ON ef.eventId = ev.eventID 
                WHERE comId = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
// IDK
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getPartiFrom(IN evid INT)
            BEGIN
                SELECT * FROM vw_eventParti WHERE eventId = evid;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // IDK
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getTeams(IN id INT)
            BEGIN
                SELECT p.*, t.teamName FROM vw_eventParti p 
                INNER JOIN vw_teams t ON p.teamId = t.teamId 
                WHERE p.contId = id;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

// NAGAMIT
try {
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
} catch (Exception $e) {
    echo $e->getMessage();
}

// IDK
$sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_updateContStat(IN parti INT)
        BEGIN
            UPDATE vw_eventParti SET status = 'finish' WHERE contId = parti;
        END ;";
if ($conn->query($sqlT) === TRUE) {
} else {
    echo "Error creating table: " . $conn->error;
}

// NAGAMIT
try {
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
} catch (Exception $e) {
    echo $e->getMessage();
}

// NAGAMIT
try {
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
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    // IDK
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getScore(IN conid INT, IN evid INT)
            BEGIN
                SELECT * FROM vw_subresult WHERE contestantId = conid AND eventId = evid;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }

    // IDK
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getScorePts(IN evid INT)
            BEGIN
                SELECT * FROM vw_eventScore WHERE eventCategory = 
                (SELECT eventCategory FROM vw_events WHERE eventID = evid);
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

/* REPORTS (admin) */
try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_scoreReport()
            BEGIN
                SELECT 
                  ve.eventName, vt.teamName, vs.total_score, sr.action_made, sr.action_at
                FROM vw_scoreReport sr
                INNER JOIN vw_subresult vs ON vs.subId = sr.result_id
                INNER JOIN vw_events ve ON ve.eventID = vs.eventId
                INNER JOIN vw_eventparti vp ON vp.contId = vs.contestantId
                INNER JOIN vw_teams vt ON vt.teamId = vp.teamId
                ORDER BY sr.action_at ASC;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getScoredEvent()
            BEGIN
                SELECT ev.eventName FROM vw_subresult sb 
                INNER JOIN vw_events ev ON ev.eventID = sb.eventId 
                GROUP BY sb.eventId;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS scoreReport (
            score_id INT AUTO_INCREMENT PRIMARY KEY,
            result_id INT NOT NULL,
            action_made VARCHAR(299) NOT NULL,
            action_at DATETIME NOT NULL,
            FOREIGN KEY (result_id) REFERENCES sub_results(subId) ON DELETE CASCADE
        );
    ";
    if ($conn->query($createTableSQL) === TRUE) {
    } else {
        throw new Exception("Error creating table: " . $conn->error);
    }

    $createViewSQL = "
        CREATE OR REPLACE VIEW vw_scoreReport AS 
        SELECT * FROM scoreReport;
    ";
    if ($conn->query($createViewSQL) === TRUE) {
    } else {
        throw new Exception("Error creating view: " . $conn->error);
    }

    // Step 3: Create the insert trigger
    $createInsertTriggerSQL = "
        CREATE TRIGGER IF NOT EXISTS tr_insertScore
        AFTER INSERT ON sub_results
        FOR EACH ROW
        BEGIN
            INSERT INTO scoreReport (result_id, action_made, action_at) 
            VALUES (NEW.subId, 'Score Inserted', NOW());
        END;
    ";

    if ($conn->query($createInsertTriggerSQL) === TRUE) {
    } else {
        throw new Exception("Error creating insert trigger: " . $conn->error);
    }

    // Step 4: Create the update trigger
    $createUpdateTriggerSQL = "
        CREATE TRIGGER IF NOT EXISTS tr_updateScore
        AFTER UPDATE ON sub_results
        FOR EACH ROW
        BEGIN
            INSERT INTO scoreReport (result_id, action_made, action_at) 
            VALUES (NEW.subId, 'Score Updated', NOW());
        END;
    ";

    if ($conn->query($createUpdateTriggerSQL) === TRUE) {
    } else {
        throw new Exception("Error creating update trigger: " . $conn->error);
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
/* REPORTS */

/* END COMMITTEE */




/* JUDGE */
try {
    // IDK
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
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    // NAGAMIT
    $sqlT = "CREATE PROCEDURE IF NOT EXISTS sp_getJudges(IN evid INT)
            BEGIN
                SELECT DISTINCT(va.userId) as perId, va.firstName AS perName FROM vw_subresult vs
                INNER JOIN vw_accounts va on vs.personnelId = va.userId
                WHERE vs.eventId = evid;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    // NAGAMIT
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
} catch (Exception $e) {
    echo $e->getMessage();
}
/* END JUDGE */


#STORED FUNCTIONS ------------------------------------------------------------------------------------------
try {
    // Stored Function for count logs NAGAMIT
    $sqlT = "CREATE FUNCTION IF NOT EXISTS fn_getLogCount(yearFilter INT) 
            RETURNS INT 
            DETERMINISTIC
            BEGIN
                DECLARE totalLogs INT;

                IF yearFilter IS NOT NULL THEN
                    SELECT COUNT(*) INTO totalLogs
                    FROM vw_logs
                    WHERE YEAR(date_on) = yearFilter;
                ELSE
                    SELECT COUNT(*) INTO totalLogs
                    FROM vw_logs;
                END IF;

                RETURN totalLogs;
            END ;";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}


try {
    // NAGAMIT
    $sqlT = "CREATE FUNCTION IF NOT EXISTS fn_getTeamCount()
            RETURNS INT
            DETERMINISTIC
            BEGIN
                DECLARE total INT;
                SELECT COUNT(*) INTO total FROM vw_teams;
                RETURN total;
            END;
            ";
    if ($conn->query($sqlT) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}



/* INDEX */
try {
    // Index for email NAGAMIT
    $createIdx = "CREATE INDEX IF NOT EXISTS idx_acc_email
            ON accounts (email)";
    if ($conn->query($createIdx) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    // Index for fullname NAGAMIT
    $createIdx = "CREATE INDEX IF NOT EXISTS idx_fullname
                ON accounts (firstName, middleName, lastName)";
    if ($conn->query($createIdx) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    // Index for role NAGAMIT
    $createIdx = "CREATE INDEX IF NOT EXISTS idx_role
                ON accounts (type)";
    if ($conn->query($createIdx) === TRUE) {
    } else {
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}



$conn->close();

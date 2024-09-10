<?php
    $servername = "localhost"; 
    $username = "root"; 
    $password = "";
    $dbname = "ilpsystem";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    #array to use
    $results = array();

    $sql = "SELECT * FROM vw_subresult;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $retval = $stmt->get_result();

    if ($retval->num_rows > 0) {
        while($row = $retval->fetch_assoc()) {
            $ev = $row['eventId'];
            $cont = $row['contestantId'];

            $results[] = array('event' => $ev, 'contestant' => $cont);
        }
    }
    $retval->free();
    $stmt->close();

    foreach($results as $res) {
        $evId = $res['event'];
        
        $query = "SELECT eventId, (select eventCategory from vw_events WHERE eventID = sub_results.eventId) as category, contestantId, SUM(total_score) AS score, 
        RANK() OVER (ORDER BY SUM(total_score) DESC) AS rank
        FROM sub_results
        WHERE eventId = $evid
        GROUP BY contestantId;";
    }

    
?>
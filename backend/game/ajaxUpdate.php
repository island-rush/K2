<?php
set_time_limit(0);

$gameId = (int) htmlentities($_REQUEST['gameId']);
$lastUpdateId = (int) htmlentities($_REQUEST['lastUpdateId']);

include("../db.php");

$loopCounter = 0;
while(true) {
    $valuecheck = 0;
    $query = 'SELECT * FROM updates WHERE (updateGameId = ?) AND (updateId > ?) ORDER BY updateId ASC';
    $query = $db->prepare($query);
    $query->bind_param("ii", $gameId, $lastUpdateId);
    $query->execute();
    $results = $query->get_result();
    $num_results = $results->num_rows;
    if ($num_results > 0) {
        $r = $results->fetch_assoc();
        $arr = array(
            'updateId' => (int) $r['updateId'],
            'updateType' => (string) $r['updateType'],
            'updatePlacementId' => (string) $r['updatePlacementId'],
            'updateNewPositionId' => (string) $r['updateNewPositionId'],
            'updateNewContainerId' => (string) $r['updateNewContainerId'],
            'updateHTML' => (string) $r['updateHTML'],
        );
        echo json_encode($arr);
        break;
    }

    if ($loopCounter++ >= 800) {  //3.5 minutes total
        echo "TIMEOUT";
        break;
    }

    usleep(250000);   //.25 seconds in-between each query
}

$results->free();
$db->close();

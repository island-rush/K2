<?php
set_time_limit(0);
$gameId = (int) $_REQUEST['gameId'];
$lastUpdateId = (int) $_REQUEST['lastUpdateId'];
include("../db.php");
$loopCounter = 0;
while(true) {
    $query = 'SELECT updateId, updateType, updatePlacementId, updateNewPositionId, updateNewContainerId, updateHTML FROM updates WHERE (updateGameId = ?) AND (updateId > ?) ORDER BY updateId ASC';
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
    if ($loopCounter++ >= 4000) {  //4000 * .025 ~= 100 seconds, change frequency if server is slow
        echo "TIMEOUT";
        break;
    }
    usleep(25000);   //25000 => .025 seconds in-between each query
}
$results->free();
$db->close();

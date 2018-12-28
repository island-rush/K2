<?php
$query = 'SELECT battlePieceId, battlePieceWasHit FROM battlePieces WHERE battleGameId = ? AND battlePieceState = ?';
$query = $db->prepare($query);
$query->bind_param("ii", $gameId, $boxId);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
for ($i = 0; $i < $num_results; $i++) {
    $r = $results->fetch_assoc();
    $battlePieceId = $r['battlePieceId'];
    $battlePieceWasHit = $r['battlePieceWasHit'];

    $query2 = 'SELECT placementUnitId, placementTeamId FROM placements WHERE placementId = ?';
    $query2 = $db->prepare($query2);
    $query2->bind_param("i", $battlePieceId);
    $query2->execute();
    $results2 = $query2->get_result();
    $r2 = $results2->fetch_assoc();
    $placementUnitId = $r2['placementUnitId'];
    $placementTeamId = $r2['placementTeamId'];

    echo "<div class='".$unitNames[$placementUnitId]." gamePiece ".$placementTeamId."' title='".$unitNames[$placementUnitId]."' data-battlePieceId='".$battlePieceId."' onclick='battlePieceClick(event, this)'></div>";
}

unset($boxId);

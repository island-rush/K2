<?php
$query = 'SELECT placementId, placementUnitId, placementTeamId, placementCurrentMoves, placementBattleUsed FROM placements WHERE placementPositionId = ? AND placementGameId = ? AND placementContainerId = -1';
$query = $db->prepare($query);
$query->bind_param("ii", $positionId, $gameId);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
for ($i = 0; $i < $num_results; $i++) {
    $r = $results->fetch_assoc();
    $placementId = (int) $r['placementId'];
    $placementUnitId = (int) $r['placementUnitId'];
    $placementTeamId = $r['placementTeamId'];
    $placementCurrentMoves = $r['placementCurrentMoves'];
    $placementBattleUsed = $r['placementBattleUsed'];
    $pieceFunctions = ' draggable="true" ondragstart="pieceDragstart(event, this);" ondragleave="pieceDragleave(event, this);" onclick="pieceClick(event, this);" ondragenter="pieceDragenter(event, this);" ';
    $containerFunctions = " ondragenter='containerDragenter(event, this);' ondragleave='containerDragleave(event, this);' ondragover='positionDragover(event, this);' ondrop='positionDrop(event, this);' ";
    $battleUsedText = "";
    if ($placementBattleUsed == 1) {
        $battleUsedText = "\nUsed in Attack";
    }
    echo "<div class='".$unitNames[$placementUnitId]." gamePiece ".$placementTeamId."' title='".$unitNames[$placementUnitId]."\nMoves: ".$placementCurrentMoves.$battleUsedText."' data-placementId='".$placementId."' ".$pieceFunctions.">";
    if ($placementUnitId == 0 || $placementUnitId == 3) {
        if ($placementUnitId == 0) {
            $classthing = "transportContainer";
        } else {
            $classthing = "aircraftCarrierContainer";
        }
        echo "<div class='".$classthing."' data-positionId='-1' ".$containerFunctions.">";  //open the container
        $query2 = 'SELECT placementId, placementUnitId, placementCurrentMoves, placementBattleUsed FROM placements WHERE (placementGameId = ?) AND (placementContainerId = ?)';
        $query2 = $db->prepare($query2);
        $query2->bind_param("ii", $gameId, $placementId);
        $query2->execute();
        $results2 = $query2->get_result();
        $num_results2 = $results2->num_rows;
        for ($b = 0; $b < $num_results2; $b++) {
            $x = $results2->fetch_assoc();
            $placementId2 = $x['placementId'];
            $placementUnitId2 = $x['placementUnitId'];
            $placementCurrentMoves2 = $x['placementCurrentMoves'];
            $battleUsedText2 = "";
            if ($x['placementBattleUsed'] == 1) {
                $battleUsedText2 = "\nUsed in Attack";
            }
            echo "<div class='".$unitNames[$placementUnitId2]." gamePiece ".$placementTeamId."' title='".$unitNames[$placementUnitId2]."\nMoves: ".$placementCurrentMoves2.$battleUsedText2."' data-placementId='".$placementId2."' ".$pieceFunctions."></div>";
        }
        echo "</div>";  //end the container
    }
    echo "</div>";  //end the overall piece
}
unset($positionId);

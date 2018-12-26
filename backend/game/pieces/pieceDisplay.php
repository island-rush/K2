<?php
if (isset($positionId)) {
    $query = 'SELECT placementId, placementUnitId, placementContainerId, placementTeamId FROM placements WHERE placementPositionId = ? AND placementGameId = ? AND placementContainerId = ?';
    $query = $db->prepare($query);
    $query->bind_param("iii", $positionId, $gameId, $out_container);
    $query->execute();
    $results = $query->get_result();
    $num_results = $results->num_rows;

    if ($num_results > 0) {
        for ($i = 0; $i < $num_results; $i++) {
            $r = $results->fetch_assoc();
            $placementContainerId = (int) $r['placementContainerId'];
            if ($placementContainerId == $out_container) {
                $placementId = (int) $r['placementId'];
                $placementUnitId = (int) $r['placementUnitId'];
                $placementTeamId = $r['placementTeamId'];

                $pieceFunctions = ' draggable="true" ondragstart="pieceDragstart(event, this);" ondragleave="pieceDragleave(event, this);" onclick="pieceClick(event, this);" ondragenter="pieceDragenter(event, this);" ';
                $containerFunctions = " ondragenter='containerDragenter(event, this);' ondragleave='containerDragleave(event, this);' ondragover='positionDragover(event, this);' ondrop='positionDrop(event, this);' ";

                //open the overall piece
                echo "<div class='".$unitNames[$placementUnitId]." gamePiece ".$placementTeamId."' title='".$unitNames[$placementUnitId]."' data-placementId='".$placementId."' ".$pieceFunctions.">";
                if ($placementUnitId == 0 || $placementUnitId == 3) {
                    if ($placementUnitId == 0) {
                        $classthing = "transportContainer";
                    } else {
                        $classthing = "aircraftCarrierContainer";
                    }
                    echo "<div class='".$classthing."' data-positionId='-1' ".$containerFunctions.">";  //open the container
                    $query2 = 'SELECT placementId, placementUnitId FROM placements WHERE (placementGameId = ?) AND (placementContainerId = ?)';
                    $query2 = $db->prepare($query2);
                    $query2->bind_param("ii", $gameId, $placementId);
                    $query2->execute();
                    $results2 = $query2->get_result();
                    $num_results2 = $results2->num_rows;
                    if ($num_results2 > 0) {
                        for ($b=0; $b < $num_results2; $b++) {
                            $x = $results2->fetch_assoc();
                            $placementId2 = $x['placementId'];
                            $placementUnitId2 = $x['placementUnitId'];
                            echo "<div class='".$unitNames[$placementUnitId2]." gamePiece ".$placementTeamId."' title='".$unitNames[$placementUnitId2]."' data-placementId='".$placementId2."' ".$pieceFunctions."></div>";
                        }
                    }
                    echo "</div>";  //end the container
                }
                echo "</div>";  //end the overall piece
            }
        }
    }
}
unset($positionId);

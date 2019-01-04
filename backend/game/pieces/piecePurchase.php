<?php
session_start();
include("../../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
$placementUnitId = (int) $_REQUEST['unitId'];
$costs = [8, 8, 10, 15, 4, 5, 6, 5, 8, 7, 8, 12, 12, 15, 11, 10];
$query = 'SELECT gameActive, gamePhase, gameCurrentTeam, game'.$myTeam.'Rpoints FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$points = $r['game'.$myTeam.'Rpoints'];
if ($r['gameActive'] != 1) {
    header("location:home.php?err=7");
    exit;
}
if ($myTeam != $gameCurrentTeam) {
    echo "Not your team's turn.";
    exit;
}
if ($gamePhase != 1) {
    echo "Not the right phase to purchase.";
    exit;
}
if ($points < $costs[$placementUnitId]) {
    echo "Not enough points.";
    exit;
}
$query = 'UPDATE games SET game'.$myTeam.'Rpoints = game'.$myTeam.'Rpoints - ? WHERE (gameId = ?)';
$query = $db->prepare($query);
$query->bind_param("ii", $costs[$placementUnitId], $gameId);
$query->execute();
$unitsMoves = [2, 2, 2, 2, 1, 1, 1, 1, 2, 3, 1, 4, 6, 5, 5, 0];
$placementPositionId = 118;
$query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementCurrentMoves, placementPositionId) VALUES(?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisii", $gameId, $placementUnitId, $myTeam, $unitsMoves[$placementUnitId], $placementPositionId);
$query->execute();
$query = 'SELECT LAST_INSERT_ID()';
$query = $db->prepare($query);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
$r = $results->fetch_assoc();
$placementId = (int) $r['LAST_INSERT_ID()'];
$unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
$pieceFunctions = ' draggable="true" ondragstart="pieceDragstart(event, this);" ondragleave="pieceDragleave(event, this);" onclick="pieceClick(event, this);" ondragenter="pieceDragenter(event, this);" ';
$containerFunctions = " ondragenter='containerDragenter(event, this);' ondragleave='containerDragleave(event, this);' ondragover='positionDragover(event, this);' ondrop='positionDrop(event, this);' ";
$pieceHTML = "<div class='".$unitNames[$placementUnitId]." gamePiece ".$myTeam."' title='".$unitNames[$placementUnitId]."\nMoves: ".$unitsMoves[$placementUnitId]."' data-placementId='".$placementId."' ".$pieceFunctions.">";
if ($placementUnitId == 0 || $placementUnitId == 3) {
    if ($placementUnitId == 0) {
        $classthing = "transportContainer";
    } else {
        $classthing = "aircraftCarrierContainer";
    }
    $pieceHTML = $pieceHTML."<div class='".$classthing."' data-positionId='-1' ".$containerFunctions."></div>";  //open the container
}
$pieceHTML = $pieceHTML."</div>";  //end the overall piece
$updateType = "piecePurchase";
$query = 'INSERT INTO updates (updateGameId, updateType, updateHTML) VALUES (?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iss", $gameId, $updateType, $pieceHTML);
$query->execute();
echo "Piece Purchased";
exit;

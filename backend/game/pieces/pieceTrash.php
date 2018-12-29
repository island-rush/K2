<?php
session_start();
include("../../db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$placementId = (int) htmlentities($_REQUEST['placementId']);

$query = 'SELECT gamePhase, gameCurrentTeam FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();

$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];

if ($myTeam != $gameCurrentTeam) {
    echo "Not your Team's turn.";
    exit;
}
if ($gamePhase != 1) {
    echo "Cannot recycle during this phase.";
    exit;
}

$query = 'SELECT placementPositionId, unitCost FROM (SELECT placementPositionId, placementUnitId FROM placements WHERE placementId = ?) a NATURAL JOIN units b WHERE placementUnitId = unitId';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $placementId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$positionId = $r['placementPositionId'];
$unitCost = $r['unitCost'];

if ($positionId != 118) {
    echo "Can only recycle pieces in inventory.";
    exit;
}

$query = 'DELETE FROM placements WHERE placementId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $placementId);
$query->execute();

$query = 'UPDATE games SET game'.$myTeam.'Rpoints = game'.$myTeam.'Rpoints + ? WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("ii", $unitCost, $gameId);
$query->execute();

$updateType = "pieceRemove";
$query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId) VALUES (?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("isi", $gameId, $updateType, $placementId);
$query->execute();

$updateType = "getBoard";
$query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
$query = $db->prepare($query);
$query->bind_param("is", $gameId, $updateType);
$query->execute();

echo "Piece recycled.";
exit;

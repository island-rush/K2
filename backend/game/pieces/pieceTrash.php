<?php
session_start();
include("../../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
$placementId = (int) $_REQUEST['placementId'];
$query = 'SELECT gameActive, gamePhase, gameCurrentTeam FROM games WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
if ($r['gameActive'] != 1) {
    header("location:index.php?err=1");
    exit;
}
if ($myTeam != $gameCurrentTeam) {
    echo "Not your Team's turn.";
    exit;
}
if ($gamePhase != 1) {
    echo "Cannot recycle during this phase.";
    exit;
}
$costs = [8, 8, 10, 15, 4, 5, 6, 5, 8, 7, 8, 12, 12, 15, 11, 10];
$query = 'SELECT placementPositionId, placementUnitId FROM placements WHERE placementId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $placementId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$positionId = $r['placementPositionId'];
$unitCost = $costs[$r['placementUnitId']];
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

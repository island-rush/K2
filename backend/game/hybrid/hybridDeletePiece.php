<?php
session_start();
include("../../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
$placementId = (int) $_REQUEST['placementId'];
$query = 'SELECT gameActive, gamePhase, gameCurrentTeam, game'.$myTeam.'Hpoints FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$points = $r['game'.$myTeam.'Hpoints'];
if ($r['gameActive'] != 1) {
    header("location:home.php?err=7");
    exit;
}
if ($myTeam != $gameCurrentTeam) {
    echo "It is not your team's turn.";
    exit;
}
if ($gamePhase != 5) {
    echo "It is not the right phase for this.";
    exit;
}
if ($points < 6) {
    echo "Not enough hybrid points.";
    exit;
}
$query = 'SELECT placementTeamId FROM placements WHERE placementId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $placementId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$placementTeamId = $r['placementTeamId'];
if ($placementTeamId == $myTeam) {
    echo "Don't delete you're own piece.";
    exit;
}
$query = 'DELETE FROM placements WHERE placementId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $placementId);
$query->execute();
$query = 'DELETE FROM placements WHERE placementContainerId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $placementId);
$query->execute();
$updateType = "pieceRemove";
$query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId) VALUES (?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("isi", $gameId, $updateType, $placementId);
$query->execute();
$query = 'UPDATE games SET game'.$myTeam.'Hpoints = game'.$myTeam.'Hpoints - 6 WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $gameId);
$query->execute();
$updateType = "getBoard";
$query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
$query = $db->prepare($query);
$query->bind_param("is", $gameId, $updateType);
$query->execute();
echo "Piece Deleted!";
exit;

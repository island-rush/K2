<?php
session_start();
include("../../db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$islandNum = (int) $_REQUEST['islandNum'];

$query = 'SELECT gamePhase, gameCurrentTeam, game'.$myTeam.'Hpoints, gameIsland'.$islandNum.' FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();

$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$points = $r['game'.$myTeam.'Hpoints'];
$gameIslandOwner = $r['gameIsland'.$islandNum];

if ($myTeam != $gameCurrentTeam) {
    echo "It is not your team's turn.";
    exit;
}
if ($gamePhase != 5) {
    echo "It is not the right phase for this.";
    exit;
}
if ($points < 4) {
    echo "Not enough hybrid points.";
    exit;
}
if ($gameIslandOwner == $myTeam) {
    echo "Don't Drain your own island.";
    exit;
}

$order = 0;
$bank = "bankAdd";
$zone = $islandNum + 100;
$length = 5;
$activated = 1;
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsEffect, newsZone, newsLength, newsActivated) VALUES(?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iissiii",$gameId, $order, $myTeam, $bank, $zone, $length, $activated);
$query->execute();

$query = 'UPDATE games SET game'.$myTeam.'Hpoints = game'.$myTeam.'Hpoints - 4 WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $gameId);
$query->execute();

$updateType = "phaseChange";
$query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
$query = $db->prepare($query);
$query->bind_param("is", $gameId, $updateType);
$query->execute();

echo "Draining the island.";
exit;

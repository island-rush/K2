<?php
session_start();
include("../../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
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
    header("location:home.php?err=1");
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
if ($points < 10) {
    echo "Not enough hybrid points.";
    exit;
}
$order = 0;
$length = 2;
$activated = 1;
$zone = 200; //all zones
$disable = "disable";
$team = "Red";
if ($myTeam == "Red") {
    $team = "Blue";
}
$aircraft = '{"transport":0, "submarine":0, "destroyer":0, "aircraftCarrier":0, "soldier":0, "artillery":0, "tank":0, "marine":0, "convoy":0, "attackHelo":1, "sam":0, "fighter":1, "bomber":1, "stealthBomber":1, "tanker":1}';
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsLength, newsActivated) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisssiii",$gameId, $order, $team, $aircraft, $disable, $zone, $length, $activated);
$query->execute();
$query = 'UPDATE games SET game'.$myTeam.'Hpoints = game'.$myTeam.'Hpoints - 10 WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $gameId);
$query->execute();
$updateType = "getBoard";
$query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
$query = $db->prepare($query);
$query->bind_param("is", $gameId, $updateType);
$query->execute();
echo "Purchased Goldeneye.";
exit;

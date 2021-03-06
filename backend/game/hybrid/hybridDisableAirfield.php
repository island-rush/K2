<?php
session_start();
include("../../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
$positionId = (int) ($_REQUEST['positionId']);
$query = 'SELECT gameActive, gamePhase, gameCurrentTeam, game'.$myTeam.'Hpoints FROM games WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$points = $r['game'.$myTeam.'Hpoints'];
if ($r['gameActive'] != 1) {
    header("location:index.php?err=1");
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
if ($points < 3) {
    echo "Not enough hybrid points.";
    exit;
}
$listairfields = [56, 57, 78, 83, 89, 113, 116, 66, 68];
if (!in_array($positionId, $listairfields)) {
    echo "Not a valid Airfield position.";
    exit;
}
$order = 0;
$length = 2;
$activated = 1;
$zone = $positionId + 1000;
$disable = "disable";
$team = "Red";
if ($myTeam == "Red") {
    $team = "Blue";
}
$aircraft = '{"transport":0, "submarine":0, "destroyer":0, "aircraftCarrier":0, "soldier":0, "artillery":0, "tank":0, "marine":0, "convoy":0, "attackHelo":0, "sam":0, "fighter":1, "bomber":1, "stealthBomber":1, "tanker":1}';
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsLength, newsActivated) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisssiii",$gameId, $order, $team, $aircraft, $disable, $zone, $length, $activated);
$query->execute();
$three = 3;
$query = 'UPDATE games SET game'.$myTeam.'Hpoints = game'.$myTeam.'Hpoints - 3 WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("i",  $gameId);
$query->execute();
$updateType = "getBoard";
$query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
$query = $db->prepare($query);
$query->bind_param("is", $gameId, $updateType);
$query->execute();
echo "Disabled the Airfield.";
exit;

<?php
session_start();
include("../../db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$query = 'SELECT gamePhase, gameCurrentTeam, game'.$myTeam.'Hpoints FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();

$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$points = $r['game'.$myTeam.'Hpoints'];

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
$aircraft = '{"Transport":0, "Submarine":0, "Destroyer":0, "AircraftCarrier":0, "ArmyCompany":0, "ArtilleryBattery":0, "TankPlatoon":0, "MarinePlatoon":0, "MarineConvoy":0, "AttackHelo":1, "SAM":0, "FighterSquadron":1, "BomberSquadron":1, "StealthBomberSquadron":1, "Tanker":1}';
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsLength, newsActivated) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisssiii",$gameId, $order, $team, $aircraft, $disable, $zone, $length, $activated);
$query->execute();

$query = 'UPDATE games SET game'.$myTeam.'Hpoints = game'.$myTeam.'Hpoints - 10 WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $gameId);
$query->execute();

$updateType = "phaseChange";
$query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
$query = $db->prepare($query);
$query->bind_param("is", $gameId, $updateType);
$query->execute();

echo "Purchased Goldeneye.";
exit;

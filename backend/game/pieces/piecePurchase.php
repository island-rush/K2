<?php
session_start();
include("../../db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$unitId = $_REQUEST['unitId'];
$costs = [8, 8, 10, 15, 4, 5, 6, 5, 8, 7, 8, 12, 12, 15, 11, 10];

$query = 'SELECT gamePhase, gameCurrentTeam, gameRedRpoints, gameBlueRpoints FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();

$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
if ($myTeam == "Red") {
    $points = $r['gameRedRpoints'];
} else {
    $points = $r['gameBlueRpoints'];
}

if ($gamePhase == 1 && $myTeam == $gameCurrentTeam && $points >= $costs[$unitId]) {
    if ($myTeam == "Red") {
        $query = 'UPDATE games SET gameRedRpoints = gameRedRpoints - ? WHERE (gameId = ?)';
    } else {
        $query = 'UPDATE games SET gameBlueRpoints = gameBlueRpoints - ? WHERE (gameId = ?)';
    }
    $query = $db->prepare($query);
    $query->bind_param("ii", $costs[$unitId], $gameId);
    $query->execute();

    $unitsMoves = [2, 2, 2, 2, 1, 1, 1, 1, 2, 3, 1, 4, 6, 5, 5, 0];
    $placementPositionId = 118;
    $query = 'INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementCurrentMoves, placementPositionId) VALUES(?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iisii", $gameId, $unitId, $myTeam, $unitsMoves[$unitId], $placementPositionId);
    $query->execute();

    $query = 'SELECT LAST_INSERT_ID()';
    $query = $db->prepare($query);
    $query->execute();
    $results = $query->get_result();
    $num_results = $results->num_rows;
    $r= $results->fetch_assoc();
    $placementId = $r['LAST_INSERT_ID()'];

    $unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
    $updateType = "piecePurchase";
    $pieceHTML = "<div class='".$unitNames[$unitId]." gamePiece ".$myTeam."' title='".$unitNames[$unitId]."' data-placementId='".$placementId."'></div>";
    $query = 'INSERT INTO updates (updateGameId, updateType, updateBattlePiecesSelected) VALUES (?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iss", $gameId, $updateType, $pieceHTML);
    $query->execute();
}
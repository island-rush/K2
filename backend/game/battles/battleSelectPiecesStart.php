<?php
session_start();
include("../../db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$positionId = (int) $_REQUEST['positionId'];

$query = 'SELECT gameActive, gamePhase, gameCurrentTeam, gameBattleSection FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();

$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$gameBattleSection = $r['gameBattleSection'];

if ($r['gameActive'] != 1) {
    header("location:home.php?err=7");
    exit;
}
if ($myTeam != $gameCurrentTeam) {
    echo "It is not your team's turn.";
    exit;
}
if ($gamePhase != 2) {
    echo "It is not the right phase for this.";
    exit;
}
if ($gameBattleSection != "selectPos") {
    echo "Cannot do this during this battleSection.";
    exit;
}
if ($positionId < 0 || $positionId > 117) {
    echo "Invalid position selected.";
    exit;
}

$query = 'SELECT placementId, placementUnitId FROM placements WHERE placementGameId = ? AND placementPositionId = ? AND placementTeamId != ?';
if ($positionId <= 54) {  //exclude land units in a transport when in water, but include fighters inside carrier
    $query = 'SELECT placementId, placementUnitId FROM placements WHERE placementGameId = ? AND placementPositionId = ? AND placementTeamId != ? AND placementUnitId != 4 AND placementUnitId != 5 AND placementUnitId != 6 AND placementUnitId != 7 AND placementUnitId != 8 AND placementUnitId != 15';
}
$query = $db->prepare($query);
$query->bind_param("iis", $gameId, $positionId, $myTeam);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;

if ($myTeam == "Red") {
    $otherTeam = "Blue";
} else {
    $otherTeam = "Red";
}

$unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
$piecesGeneratedHTML = "";
for ($i = 0; $i < $num_results; $i++) {
    $r = $results->fetch_assoc();
    $placementUnitId = $r['placementUnitId'];
    $placementId = $r['placementId'];

    $pieceState = 2;  //unused defender
    $query2 = 'INSERT INTO battlePieces (battlePieceId, battleGameId, battlePieceState) VALUES(?, ?, ?)';
    $query2 = $db->prepare($query2);
    $query2->bind_param("iii", $placementId, $gameId, $pieceState);
    $query2->execute();

    $piecesGeneratedHTML = $piecesGeneratedHTML."<div class='".$unitNames[$placementUnitId]." gamePiece ".$otherTeam."' title='".$unitNames[$placementUnitId]."' data-battlePieceId='".$placementId."' onclick='battlePieceClick(event, this)'></div>";
}

$updateType = "posSelected";
$query = 'INSERT INTO updates (updateGameId, updateType, updateHTML) VALUES (?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iss", $gameId, $updateType, $piecesGeneratedHTML);
$query->execute();

$newBattleSection = "selectPieces";
$query = 'UPDATE games SET gameBattleSection = ?, gameBattlePosSelected = ? WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("sii", $newBattleSection, $positionId, $gameId);
$query->execute();

$updateType = "getBoard";
$query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
$query = $db->prepare($query);
$query->bind_param("is", $gameId, $updateType);
$query->execute();

echo "Select Pieces for Battle";
exit;


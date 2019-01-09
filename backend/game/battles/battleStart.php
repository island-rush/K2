<?php
session_start();
include("../../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
$selectedPieces = json_decode($_REQUEST['selectedPieces']);
$query = 'SELECT gameActive, gamePhase, gameCurrentTeam, gameBattleSection, gameBattlePosSelected FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$gameBattleSection = $r['gameBattleSection'];
$gameBattlePosSelected = $r['gameBattlePosSelected'];
if ($r['gameActive'] != 1) {
    header("location:home.php?err=1");
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
if ($gameBattleSection != "selectPieces") {
    echo "Wrong battle section for this.";
    exit;
}
$unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
$piecesSelectedHTML = "";
$arrayOfPlacementMoves = [];
for ($i = 0; $i < sizeof($selectedPieces); $i++) {
    $placementId = (int) $selectedPieces[$i];
    $query = 'SELECT placementUnitId, placementPositionId, placementBattleUsed, placementCurrentMoves FROM placements WHERE (placementId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("i", $placementId);
    $query->execute();
    $results = $query->get_result();
    $r = $results->fetch_assoc();
    $placementUnitId = $r['placementUnitId'];
    $placementPositionId = $r['placementPositionId'];
    $placementBattleUsed = $r['placementBattleUsed'];
    $placementCurrentMoves = $r['placementCurrentMoves'];
    if ($placementBattleUsed != 0) {
        echo "Selected Piece that was used in battle.";
        exit;
    }
    if ($placementPositionId != $gameBattlePosSelected) {
        if ($_SESSION['dist'][$gameBattlePosSelected][$placementPositionId] > 1) {
            echo "Piece Selected was out of range.";
            exit;
        }
        if ($placementUnitId >= 11) {
            echo "Selected a plane not in the same position.";
            exit;
        }

    }
    $pieceState = 1;  //unused attacker
    $query2 = 'INSERT INTO battlePieces (battlePieceId, battleGameId, battlePieceState) VALUES(?, ?, ?)';
    $query2 = $db->prepare($query2);
    $query2->bind_param("iii", $placementId, $gameId, $pieceState);
    $query2->execute();
    $query = 'UPDATE placements SET placementBattleUsed = 1 WHERE (placementId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("i", $placementId);
    $query->execute();
    array_push($arrayOfPlacementMoves, array($placementId, $placementUnitId, $placementCurrentMoves, 1));
    $piecesSelectedHTML = $piecesSelectedHTML."<div class='".$unitNames[$placementUnitId]." gamePiece ".$myTeam."' title='".$unitNames[$placementUnitId]."' data-battlePieceId='".$placementId."' onclick='battlePieceClick(event, this)'></div>";
}
if (sizeof($arrayOfPlacementMoves) > 0) {
    $JSONArray = json_encode($arrayOfPlacementMoves);
    $updateType = "updateMoves";
    $query = 'INSERT INTO updates (updateGameId, updateType, updateHTML) VALUES (?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iss", $gameId, $updateType, $JSONArray);
    $query->execute();
}
$updateType = "piecesSelected";
$query = 'INSERT INTO updates (updateGameId, updateType, updateHTML) VALUES (?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iss", $gameId, $updateType, $piecesSelectedHTML);
$query->execute();
$newBattleSection = "attack";
$query = 'UPDATE games SET gameBattleSection = ? WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("si", $newBattleSection, $gameId);
$query->execute();
$updateType = "getBoard";
$query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
$query = $db->prepare($query);
$query->bind_param("is", $gameId, $updateType);
$query->execute();
echo "Battle Started!";
exit;

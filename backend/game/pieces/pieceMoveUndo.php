<?php
session_start();
include("../../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
$query = 'SELECT gameActive, gamePhase, gameCurrentTeam, gameBattleSection FROM games WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$gameBattleSection = $r['gameBattleSection'];
if ($r['gameActive'] != 1) {
    header("location:index.php?err=1");
    exit;
}
if ($myTeam != $gameCurrentTeam) {
    echo "Cannot undo, not your team's turn.";
    exit;
}
if ($gamePhase != 2 && $gamePhase != 3 && $gamePhase != 4) {
    echo "Cannot undo during this phase.";
    exit;
}
if ($gameBattleSection != "none") {
    echo "Cannot undo during battle";
    exit;
}
$query = 'SELECT movementId, movementFromPosition, movementFromContainer, movementNowPlacement FROM movements WHERE movementGameId = ? ORDER BY movementId DESC LIMIT 0, 1';
$query = $db->prepare($query);
$query->bind_param("i", $gameId);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
if ($num_results > 0) {
    $r = $results->fetch_assoc();
    $movementId = $r['movementId'];
    $movementFromPosition = $r['movementFromPosition'];
    $movementFromContainer = $r['movementFromContainer'];
    $movementPlacementId = $r['movementNowPlacement'];
    
    
    $query = 'SELECT placementPositionId FROM placements WHERE placementId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $movementPlacementId);
    $query->execute();
    $results = $query->get_result();
    $r = $results->fetch_assoc();
    $oldPositionId = $r['placementPositionId'];
    
    $dist = 1;
    $query = 'UPDATE placements SET placementPositionId = ?, placementCurrentMoves = placementCurrentMoves + 1, placementContainerId = ? WHERE (placementId = ?)';
    if ($movementFromPosition == $oldPositionId) {
        $dist = 0;
        $query = 'UPDATE placements SET placementPositionId = ?, placementContainerId = ? WHERE (placementId = ?)';
    }
    
    $query = $db->prepare($query);
    $query->bind_param("iii", $movementFromPosition,  $movementFromContainer,  $movementPlacementId);
    $query->execute();
    $query = 'DELETE FROM movements WHERE movementId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $movementId);
    $query->execute();
    $query = 'SELECT placementUnitId, placementCurrentMoves, placementBattleUsed FROM placements WHERE placementId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $movementPlacementId);
    $query->execute();
    $results = $query->get_result();
    $r = $results->fetch_assoc();
    $placementUnitId = $r['placementUnitId'];
    $placementCurrentMoves = $r['placementCurrentMoves'];
    $placementBattleUsed = $r['placementBattleUsed'];
    $unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
    $battleUsedText = "";
    if ($placementBattleUsed == 1) {
        $battleUsedText = "\nUsed in Attack";
    }
    $newTitle = $unitNames[$placementUnitId]."\nMoves: ".$placementCurrentMoves.$battleUsedText;
    $updateType = "pieceMove";
    $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateNewPositionId, updateNewContainerId, updateHTML) VALUES (?, ?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("isiiis", $gameId, $updateType, $movementPlacementId, $movementFromPosition, $movementFromContainer, $newTitle);
    $query->execute();
    echo "Movement Undone.";
    exit;
} else {
    echo "No more undo's can be made.";
    exit;
}

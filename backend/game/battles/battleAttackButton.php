<?php
session_start();
include("../../db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$query = 'SELECT gamePhase, gameCurrentTeam, gameBattleSection, gameBattleSubSection FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();

$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$gameBattleSection = $r['gameBattleSection'];
$gameBattleSubSection = $r['gameBattleSubSection'];

if ($gamePhase != 2) {
    echo "It is not the right phase for this.";
    exit;
}
if ($gameBattleSubSection != "choosing_pieces" || $gameBattleSection == "none" || $gameBattleSection == "selectPos" || $gameBattleSection == "selectPieces") {
    echo "Unable to click battle piece, wrong section / subsection.";
    exit;
}
if ((($gameBattleSection == "attack" || $gameBattleSection == "askRepeat") && $myTeam != $gameCurrentTeam) || ($gameBattleSection == "counter" && $myTeam == $gameCurrentTeam)) {
    echo "Not your turn to select pieces.";
    exit;
}

if ($gameBattleSection == "attack" || $gameBattleSection == "counter") {
    if ($gameBattleSection == "attack") {  //always going to be choosing pieces, otherwise wouldn't hit this button
        $order = "ASC";  // 3 attacking 4
    } else {
        $order = "DESC"; // 4 attacking 3
    }

    $query = 'SELECT placementId, placementUnitId FROM battlePieces RIGHT JOIN placements ON battlePieceId WHERE battlePieceId = placementId AND battleGameId = ? AND (battlePieceState = 3 or battlePieceState = 4) ORDER BY battlePieceState '.$order;
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("i", $gameId);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $numResults = $results->num_rows;
    if ($numResults != 2) {
        echo "Don't have 2 battle pieces selected.";
        exit;
    }

//    $lastRoll = rand(1, 6);  //TODO: Make this the main one
//    $lastRoll = 1;
    $lastRoll = 6;

    $r = $results->fetch_assoc();
    $attackId = $r['placementId'];
    $attackUnitId = $r['placementUnitId'];
    $r = $results->fetch_assoc();
    $defendId = $r['placementId'];
    $defendUnitId = $r['placementUnitId'];

    if ($_SESSION['attack'][$attackUnitId][$defendUnitId] == 0) {
        echo "This Piece is unable to attack that piece, select a different one or end turn.";
        exit;
    }

    $wasHit = ($lastRoll >= $_SESSION['attack'][$attackUnitId][$defendUnitId]);
    $unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
    if ($wasHit) {
        $gameBattleLastMessage = $unitNames[$attackUnitId]." Hit ".$unitNames[$defendUnitId];

        $query = 'UPDATE battlePieces SET battlePieceWasHit = 1 WHERE battlePieceId = ?';
        $query = $db->prepare($query);
        $query->bind_param("i", $defendId);
        $query->execute();
    } else {
        $gameBattleLastMessage = $unitNames[$attackUnitId]." Missed ".$unitNames[$defendUnitId];
    }

    if ($gameBattleSection == "attack" && $wasHit) {
        $nextSubSection = "defense_bonus";
    } else {
        $nextSubSection = "continue_choosing";
    }

    $query = 'UPDATE games SET gameBattleSubSection = ?, gameBattleLastMessage = ?, gameBattleLastRoll = ? WHERE gameId = ?';
    $query = $db->prepare($query);
    $query->bind_param("ssii", $nextSubSection, $gameBattleLastMessage, $lastRoll, $gameId);
    $query->execute();

    $updateType = "getBoard";
    $query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("is", $gameId, $updateType);
    $query->execute();

    echo "Attacked!";
    exit;
} else {  //askRepeat, change the battle section to attack again (assume no pieces in center)
    $newSection = "attack";
    $query = 'UPDATE games SET gameBattleSection = ? WHERE gameId = ?';
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("si", $newSection, $gameId);
    $preparedQuery->execute();

    $updateType = "getBoard";
    $query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';  //need to make board look like selecting stuff
    $query = $db->prepare($query);
    $query->bind_param("is", $gameId, $updateType);
    $query->execute();

    echo "Switched Battle Turn.";
    exit;
}


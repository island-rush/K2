<?php
session_start();
include("../../db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$query = 'SELECT gamePhase, gameCurrentTeam, gameBattleSection, gameBattleSubSection, gameBattleTurn, gameBattlePosSelected FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();

$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$gameBattleSection = $r['gameBattleSection'];
$gameBattleSubSection = $r['gameBattleSubSection'];
$gameBattleTurn = $r['gameBattleTurn'];
$gameBattlePosSelected = $r['gameBattlePosSelected'];

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

    $query = 'SELECT placementId, placementUnitId, placementTeamId FROM battlePieces RIGHT JOIN placements ON battlePieceId WHERE battlePieceId = placementId AND battleGameId = ? AND (battlePieceState = 3 or battlePieceState = 4) ORDER BY battlePieceState '.$order;
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("i", $gameId);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $numResults = $results->num_rows;
    if ($numResults != 2) {
        echo "Don't have 2 battle pieces selected.";
        exit;
    }

    $lastRoll = rand(1, 6);

    $r = $results->fetch_assoc();
    $attackId = $r['placementId'];
    $attackUnitId = $r['placementUnitId'];
    $attackTeamId = $r['placementTeamId'];
    $r = $results->fetch_assoc();
    $defendId = $r['placementId'];
    $defendUnitId = $r['placementUnitId'];

    if ($attackUnitId == 4 || $attackUnitId == 9) {  //check for boosted attack
        $query = 'SELECT placementUnitId FROM battlePieces RIGHT JOIN placements ON battlePieceId WHERE battlePieceId = placementId AND battleGameId = ? AND placementTeamId = ?';
        $preparedQuery = $db->prepare($query);
        $preparedQuery->bind_param("is", $gameId, $attackTeamId);
        $preparedQuery->execute();
        $results = $preparedQuery->get_result();
        $numResults = $results->num_rows;
        for ($i = 0; $i < $numResults; $i++) {
            $r = $results->fetch_assoc();
            $placementUnitId = $r['placementUnitId'];  //armycompany boosted by artillery, heli boosted by marine
            if (($attackUnitId == 4 && $placementUnitId == 5) || ($attackUnitId == 9 && $placementUnitId == 7)) {
                if ($lastRoll != 6){
                    $lastRoll++;
                }
            }
        }
    }

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

    if (($gameBattleSection == "attack" && $wasHit) && !($gameBattlePosSelected > 54 && $attackUnitId == 2)) {
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
    //kick out bombardment destroyers
    if (++$gameBattleTurn == 1 && $gameBattlePosSelected > 54) {  //land battle
        $query = 'SELECT battlePieceId FROM battlePieces RIGHT JOIN placements ON battlePieceId WHERE battlePieceId = placementId AND battleGameId = ? AND (battlePieceState = 1 or battlePieceState = 3 or battlePieceState = 5) AND (placementUnitId = 2)';
        $preparedQuery = $db->prepare($query);
        $preparedQuery->bind_param("i", $gameId);
        $preparedQuery->execute();
        $results = $preparedQuery->get_result();
        $numResults = $results->num_rows;
        for ($i = 0; $i < $numResults; $i++) {
            $r = $results->fetch_assoc();
            $battlePieceId = $r['battlePieceId'];
            $query = 'DELETE FROM battlePieces WHERE battlePieceId = ?';
            $query = $db->prepare($query);
            $query->bind_param("i", $battlePieceId);
            $query->execute();

            $updateType = "battleRemove";
            $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId) VALUES (?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("isi", $gameId, $updateType, $battlePieceId);
            $query->execute();
        }
    }

    //kick out planes
    if ($gameBattleTurn == 2) {
        $query = 'SELECT battlePieceId FROM battlePieces RIGHT JOIN placements ON battlePieceId WHERE battlePieceId = placementId AND battleGameId = ? AND (battlePieceState = 1 or battlePieceState = 3 or battlePieceState = 5) AND (placementUnitId > 10)';
        $preparedQuery = $db->prepare($query);
        $preparedQuery->bind_param("i", $gameId);
        $preparedQuery->execute();
        $results = $preparedQuery->get_result();
        $numResults = $results->num_rows;
        for ($i = 0; $i < $numResults; $i++) {
            $r = $results->fetch_assoc();
            $battlePieceId = $r['battlePieceId'];
            $query = 'DELETE FROM battlePieces WHERE battlePieceId = ?';
            $query = $db->prepare($query);
            $query->bind_param("i", $battlePieceId);
            $query->execute();

            $updateType = "battleRemove";
            $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId) VALUES (?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("isi", $gameId, $updateType, $battlePieceId);
            $query->execute();
        }
    }



    $newSection = "attack";
    $query = 'UPDATE games SET gameBattleSection = ?, gameBattleTurn = '.$gameBattleTurn.' WHERE gameId = ?';
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


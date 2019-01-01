<?php
session_start();
include("../../db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$battlePieceId = (int) $_REQUEST['battlePieceId'];

$query = 'SELECT gameActive, gamePhase, gameCurrentTeam, gameBattleSection, gameBattleSubSection FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();

$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$gameBattleSection = $r['gameBattleSection'];
$gameBattleSubSection = $r['gameBattleSubSection'];

if ($r['gameActive'] != 1) {
    header("location:home.php?err=7");
    exit;
}
if ($gamePhase != 2) {
    echo "It is not the right phase for this.";
    exit;
}
if ($gameBattleSubSection != "choosing_pieces" || $gameBattleSection == "none" || $gameBattleSection == "selectPos" || $gameBattleSection == "selectPieces" || $gameBattleSection == "askRepeat") {
    echo "Unable to click battle piece, wrong section / subsection.";
    exit;
}
if (($gameBattleSection == "attack" && $myTeam != $gameCurrentTeam) || ($gameBattleSection == "counter" && $myTeam == $gameCurrentTeam)) {
    echo "Not your turn to select pieces.";
    exit;
}

$query = 'SELECT battlePieceState FROM battlePieces WHERE battlePieceId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $battlePieceId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$battlePieceState = $r['battlePieceState'];

if ($battlePieceState == 5 || $battlePieceState == 6) {
    echo "Cannot click used pieces.";
    exit;
}

if ($battlePieceState == 3 || $battlePieceState == 4) {
    $query = 'UPDATE battlePieces SET battlePieceState = battlePieceState - 2 WHERE battlePieceId = ?';
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("i", $battlePieceId);
    $preparedQuery->execute();

    $updateType = "battleMove";
    $newPositionId = $battlePieceState - 2;
    $battle_outcome = "";
    $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateNewPositionId, updateHTML) VALUES (?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("isiis", $gameId, $updateType, $battlePieceId, $newPositionId, $battle_outcome);
    $query->execute();

    echo "Battle Piece Clicked.";
    exit;
} else {
    $stateToCheck = $battlePieceState + 2;
    $query = 'SELECT battlePieceState FROM battlePieces WHERE battlegameId = ? AND battlePieceState = ?';
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("ii", $battlePieceId, $stateToCheck);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $num_results = $results->num_rows;
    if ($num_results != 0) {
        echo "Piece already in the center.";
        exit;
    }

    $battle_outcome = "";

    $query = 'UPDATE battlePieces SET battlePieceState = battlePieceState + 2 WHERE battlePieceId = ?';
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("i", $battlePieceId);
    $preparedQuery->execute();

    if ($gameBattleSection == "attack") {
        $order = "ASC";  // 3 attacking 4
    } else {
        $order = "DESC"; // 4 attacking 3
    }
    $query = 'SELECT placementUnitId FROM battlePieces RIGHT JOIN placements ON battlePieceId WHERE battlePieceId = placementId AND battleGameId = ? AND (battlePieceState = 3 or battlePieceState = 4) ORDER BY battlePieceState '.$order;
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("i", $gameId);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $numResults = $results->num_rows;
    if ($numResults == 2) {
        $r = $results->fetch_assoc();
        $attackUnitId = $r['placementUnitId'];
        $r = $results->fetch_assoc();
        $defendUnitId = $r['placementUnitId'];

        $valueNeeded = $_SESSION['attack'][$attackUnitId][$defendUnitId];
        $battle_outcome = "You must roll a ".$valueNeeded." or higher in order to hit.";

        echo "Click Attack to Attack!";
    } else {
        echo "Battle Piece Clicked.";
    }

    $updateType = "battleMove";
    $newPositionId = $battlePieceState + 2;
    $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateNewPositionId, updateHTML) VALUES (?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("isiis", $gameId, $updateType, $battlePieceId, $newPositionId, $battle_outcome);
    $query->execute();
}

<?php
session_start();
include("../../db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$battlePieceId = (int) $_REQUEST['battlePieceId'];

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
    $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateNewPositionId) VALUES (?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("isii", $gameId, $updateType, $battlePieceId, $newPositionId);
    $query->execute();
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

    $query = 'UPDATE battlePieces SET battlePieceState = battlePieceState + 2 WHERE battlePieceId = ?';
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("i", $battlePieceId);
    $preparedQuery->execute();

    $updateType = "battleMove";
    $newPositionId = $battlePieceState + 2;
    $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateNewPositionId) VALUES (?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("isii", $gameId, $updateType, $battlePieceId, $newPositionId);
    $query->execute();
}

$query3 = "SELECT battlePieceId FROM battlePieces WHERE battleGameId = ? AND (battlePieceState = 3 OR battlePieceState = 4)";
$preparedQuery3 = $db->prepare($query3);
$preparedQuery3->bind_param("i", $gameId);
$preparedQuery3->execute();
$results3 = $preparedQuery3->get_result();
$numResults3 = $results3->num_rows;
if ($numResults3 == 2) {
    echo "Click Attack to Attack!";
    exit;
} else {
    echo "Battle Piece Clicked.";
    exit;
}

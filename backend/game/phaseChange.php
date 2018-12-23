<?php
session_start();
include("../db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$query = 'SELECT gamePhase, gameCurrentTeam, gameBattleSection FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();

$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$gameBattleSection = $r['gameBattleSection'];

if ($gameBattleSection == "none" && $myTeam == $gameCurrentTeam) {
    $newPhaseNum = ($gamePhase + 1) % 7;
    $query = 'UPDATE games SET gamePhase = ?, gameTurn = gameTurn + 1 WHERE (gameId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("ii", $newPhaseNum, $gameId);
    $query->execute();

    $updateType = "phaseChange";
    $query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("is", $gameId, $updateType);
    $query->execute();
}









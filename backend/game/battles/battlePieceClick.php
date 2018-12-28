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



//needs to be choosing pieces
























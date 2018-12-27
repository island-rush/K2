<?php
session_start();
include("../../db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$query = 'SELECT gamePhase, gameCurrentTeam, game'.$myTeam.'Hpoints FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();

$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$points = $r['game'.$myTeam.'Hpoints'];

if ($myTeam != $gameCurrentTeam) {
    echo "It is not your team's turn.";
    exit;
}
if ($gamePhase != 5) {
    echo "It is not the right phase for this.";
    exit;
}
if ($points < 3) {
    echo "Not enough hybrid points.";
    exit;
}

$nuke = "nukeHuman";
$query4 = "SELECT newsId FROM newsAlerts WHERE newsGameId = ? AND newsActivated = 1 AND newsEffect = ? AND newsLength >= 1 AND newsTeam = ? ORDER BY newsOrder DESC";
$preparedQuery4 = $db->prepare($query4);
$preparedQuery4->bind_param("iss", $gameId, $nuke, $myTeam);
$preparedQuery4->execute();
$results4 = $preparedQuery4->get_result();
$number_results = $results4->num_rows;
if ($number_results == 0) {
    $query4 = "SELECT newsHumanitarian FROM newsAlerts WHERE newsGameId = ? AND newsActivated = 1 AND newsLength >= 1 ORDER BY newsOrder DESC";
    $preparedQuery4 = $db->prepare($query4);
    $preparedQuery4->bind_param("i", $gameId);
    $preparedQuery4->execute();
    $results4 = $preparedQuery4->get_result();
    $r4= $results4->fetch_assoc();
    if ($r4['newsHumanitarian'] == 1) {
        $query = 'UPDATE games SET game'.$myTeam.'Hpoints = game'.$myTeam.'Hpoints - 3, game'.$myTeam.'Rpoints = game'.$myTeam.'Rpoints + 10 WHERE gameId = ?';
        $query = $db->prepare($query);
        $query->bind_param("i",$gameId);
        $query->execute();

        $updateType = "phaseChange";
        $query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
        $query = $db->prepare($query);
        $query->bind_param("is", $gameId, $updateType);
        $query->execute();

        echo "Purchased Humanitarian Option.";
        exit;
    } else {
        echo "Cannot use this option, no one in need.";
        exit;
    }
} else {
    echo "Cannot use this option, nuked someone.";
    exit;
}




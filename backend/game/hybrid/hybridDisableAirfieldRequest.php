<?php
session_start();
include("../../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
$query = 'SELECT gameActive, gamePhase, gameCurrentTeam, game'.$myTeam.'Hpoints FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$points = $r['game'.$myTeam.'Hpoints'];
if ($r['gameActive'] != 1) {
    header("location:index.php?err=1");
    exit;
}
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
echo "Select Airfield to Disable.";
exit;

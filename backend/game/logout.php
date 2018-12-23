<?php
session_start();
include("../db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

if ($myTeam == "Spec") {
    header("location:../../home.php");
    exit;
}

$notJoined = 0;

if ($myTeam == "Red") {
    $query = 'UPDATE games SET gameRedJoined = ? WHERE gameId = ?';
} else {
    $query = 'UPDATE games SET gameBlueJoined = ? WHERE gameId = ?';
}

$query = $db->prepare($query);
$query->bind_param("ii", $notJoined, $gameId);
$query->execute();

$db->close();

session_unset();  //not sure capabilities of this yet (or how to fully delete the session stuff)

header("location:../../home.php");
exit;

<?php
session_start();
include("../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
if ($myTeam == "Spec") {
    header("location:../../index.php");
    exit;
}
$query = 'UPDATE games SET game'.$myTeam.'Joined = 0 WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $gameId);
$query->execute();
$db->close();
session_unset();  //not sure capabilities of this yet (or how to fully delete the session stuff)
header("location:../../index.php?logout=1");

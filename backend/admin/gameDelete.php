<?php
session_start();
if (!isset($_SESSION['secretCourseDirectorVariable'])) {
    header("location:index.php?err=8");
    exit;
}

include("../db.php");

$gameId = (int) mysqli_real_escape_string($db, $_POST['gameId']);

$query = "DELETE FROM placements WHERE placementGameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$query = "DELETE FROM movements WHERE movementGameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$query = "DELETE FROM battlePieces WHERE battleGameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$query = "DELETE FROM newsAlerts WHERE newsGameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$query = "DELETE FROM updates WHERE updateGameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$query = "DELETE FROM games where gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();

header('location:../../courseDirector.php?deletedGame=true');

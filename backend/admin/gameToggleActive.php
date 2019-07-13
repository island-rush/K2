<?php
session_start();
include("../db.php");
if (!isset($_SESSION['secretAdminSessionVariable']) || !isset($_SESSION['gameId']) || !isset($_SESSION['gameSection']) || !isset($_SESSION['gameInstructor'])) {
    header("location:index.php?err=8");
    exit;
}
$gameId = $_SESSION['gameId'];

$query = "UPDATE games SET gameActive = (gameActive + 1) % 2, gameRedJoined = 0, gameBlueJoined = 0  WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i",  $gameId);
$preparedQuery->execute();

$updateType = "logout";
$query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
$query = $db->prepare($query);
$query->bind_param("is", $gameId, $updateType);
$query->execute();

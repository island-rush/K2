<?php
session_start();
include("../db.php");

if (!isset($_SESSION['secretAdminSessionVariable'])) {
    header("location:home.php?err=4");
    exit;
}

$gameId = (int) $_SESSION['gameId'];

$old1order = (int) $_POST['swap1order'];
$old2order = (int) $_POST['swap2order'];

$tempOrder = 999;

$query = "UPDATE newsAlerts SET newsOrder = ? WHERE newsGameId = ? AND newsOrder = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("iii", $tempOrder, $gameId, $old1order);
$preparedQuery->execute();

$query = "UPDATE newsAlerts SET newsOrder = ? WHERE newsGameId = ? AND newsOrder = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("iii", $old1order, $gameId, $old2order);
$preparedQuery->execute();

$query = "UPDATE newsAlerts SET newsOrder = ? WHERE newsGameId = ? AND newsOrder = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("iii", $old2order, $gameId, $tempOrder);
$preparedQuery->execute();

header("location:../../admin.php");
exit;

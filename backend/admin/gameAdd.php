<?php
session_start();
if (!isset($_SESSION['secretCourseDirectorVariable'])) {
    header("location:index.php?err=8");
    exit;
}
include("../db.php");

$section = mysqli_real_escape_string($db, $_POST['adminSection']);
$instructor = mysqli_real_escape_string($db, $_POST['adminInstructor']);
$password = md5(mysqli_real_escape_string($db, $_POST['adminPassword']));

$query = "INSERT INTO games (gameSection, gameInstructor, gameAdminPassword) VALUES (?, ?, ?)";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("sss", $section, $instructor, $password);
$preparedQuery->execute();

$query2 = 'SELECT LAST_INSERT_ID()';
$query2 = $db->prepare($query2);
$query2->execute();
$results2 = $query2->get_result();
$r2 = $results2->fetch_assoc();
$gameId = $r2['LAST_INSERT_ID()'];

$_SESSION['gameId'] = $gameId;

include("gamePopulate.php");

header('location:../../courseDirector.php');

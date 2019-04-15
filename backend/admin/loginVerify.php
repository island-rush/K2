<?php
session_start();
if ( (isset($_POST['adminSection'])) && (isset($_POST['adminInstructor'])) && (isset($_POST['adminPassword'])) ){
    include("../db.php");
    $section = mysqli_real_escape_string($db, $_POST['adminSection']);
    $instructor = mysqli_real_escape_string($db, $_POST['adminInstructor']);
    $password = md5(mysqli_real_escape_string($db, $_POST['adminPassword']));
    $query = "SELECT gameId FROM GAMES WHERE gameSection = ? AND gameInstructor = ? AND gameAdminPassword = ?";
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("sss", $section, $instructor, $password);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $numRows = $results->num_rows;
    switch($numRows){
        case 0:
            header("location:../../home.php?err=7");  //Game does not exist
            break;
        case 1:
            $r = $results->fetch_assoc();
            $_SESSION['gameId'] = $r['gameId'];
            $_SESSION['secretAdminSessionVariable'] = "SpencerIsCool";
            $_SESSION['gameSection'] = $section;
            $_SESSION['gameInstructor'] = $instructor;
            header("location:../../admin.php");
            break;
        default:
            header("location:../../home.php?err=5");  //Multiple games exist :(
            break;
    }
    $db->close();
} else {
    header("location:../../home.php?err=6");  //Came to this file without sending everything
}

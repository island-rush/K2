<?php
session_start();
include("../db.php");
if ( (isset($_POST['gameSection'])) && (isset($_POST['gameInstructor'])) && (isset($_POST['gameTeam'])) ){
    $section = mysqli_real_escape_string($db, $_POST['gameSection']);
    $instructor = mysqli_real_escape_string($db, $_POST['gameInstructor']);
    $team = mysqli_real_escape_string($db, $_POST['gameTeam']);
    $query = "SELECT gameId, gameActive, gameRedJoined, gameBlueJoined FROM GAMES WHERE gameSection = ? AND gameInstructor = ?";
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("ss", $section,$instructor);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $numResults = $results->num_rows;
    if ($numResults == 1){
        $r = $results->fetch_assoc();
        $_SESSION['myTeam'] = $team;
        $_SESSION['gameId'] = $r['gameId'];
        $_SESSION['gameSection'] = $section;
        $_SESSION['gameInstructor'] = $instructor;
        if ($r['gameActive'] == 0 && $_SESSION['myTeam'] != "Spec") {
            header("location:../../index.php?err=1");
            exit;
        }
        if ($team == "Red") {
            if ($r['gameRedJoined'] == 1) {
                header("location:../../index.php?err=2");
                exit;
            }
            $query = 'UPDATE games SET gameRedJoined = 1 WHERE (gameId = ?)';
            $query = $db->prepare($query);
            $joinedValue = 1;
            $query->bind_param("i", $r['gameId']);
            $query->execute();
        } else if ($team == "Blue") {
            if ($r['gameBlueJoined'] == 1) {
                header("location:../../index.php?err=3");
                exit;
            }
            $query = 'UPDATE games SET gameBlueJoined = 1 WHERE (gameId = ?)';
            $query = $db->prepare($query);
            $joinedValue = 1;
            $query->bind_param("i", $r['gameId']);
            $query->execute();
        } else if ($team == "Spec") {
            //do nothing for spectator
        } else {
            header("location:../../index.php?err=4");  //bad value for team
            exit;
        }
        if (($handle = fopen('../matrices/adjMatrix.csv', "r")) !== FALSE) {
            $counter = 0;
            while(($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                $arraySize = count($data);
                for ($i=0; $i < $arraySize; $i++) {
                    $_SESSION['dist'][$counter][$i] = $data[$i];
                }
                $counter++;
            }
        }
        fclose($handle);
        if (($handle = fopen('../matrices/attackMatrix.csv', "r")) !== FALSE) {
            $counter = 0;
            while(($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                $arraySize = count($data);
                for ($i=0; $i < $arraySize; $i++) {
                    $_SESSION['attack'][$counter][$i] = $data[$i];
                }
                $counter++;
            }
        }
        fclose($handle);
        header("location:../../game.php");
    } else {
        if ($numResults == 0) {
            header("location:../../index.php?err=7");  //game does not exist
        } else {
            header("location:../../index.php?err=5");  //multiple games
        }
    }
} else {
    header("location:../../index.php?err=6");  //did not send all of the submit values to log in
}
$db->close();

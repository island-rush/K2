<?php
//Environment Variables for Database Connection
// $hostname = getenv('DB_HOSTNAME');
// $user = getenv('DB_USERNAME');
// $password = getenv('DB_PASSWORD');
// $database = getenv('DB_NAME');

// $courseDirectorLastName = getenv('CD_LASTNAME'); //ensure this value is lowercase
// $courseDirectorPasswordHash = getenv('CD_PASSWORD');

//Hard-Coded values for Database Connection
$hostname = "localhost";
$user = "root";
$password = "";
$database = "islandrushdb";

$courseDirectorLastName = "smith"; //ensure this value is lowercase
$courseDirectorPasswordHash = "5f4dcc3b5aa765d61d8327deb882cf99"; //'password'

@ $db = new mysqli($hostname, $user, $password, $database);
if (mysqli_connect_errno()) {
    echo 'ERROR: Could not connect to database. Error is '.mysqli_connect_error();
    exit;
}
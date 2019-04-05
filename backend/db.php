<?php
$hostname = 'LOCALHOST';
$user = 'root';
$password = '';
$database = 'islandRushDB';
@ $db = new mysqli($hostname, $user, $password, $database);
if (mysqli_connect_errno()) {
    echo 'ERROR: Could not connect to database.  Error is '.mysqli_connect_error();
    echo 'Hostname: '.$hostname.' User: '.$user.' Password: #### Database: '.$database;
    exit;
}
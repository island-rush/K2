<?php
$hostname = '';
$user = '';
$password = '';
$database = 'islandRushDB';
@ $db = new mysqli($hostname, $user, $password, $database);
if (mysqli_connect_errno()) {
    //Note: These error messages are not secure
    echo 'ERROR: Could not connect to database.  Error is '.mysqli_connect_error();
    echo 'DB may be down, double check backend/db.php -> Hostname: '.$hostname.' User: '.$user.' Password: #### Database: '.$database;
    exit;
}
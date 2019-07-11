<?php
//TODO: Put these into environment variables for deployment
$hostname = 'changeMe.changeMe.com';
$user = 'user@name';
$password = 'PasswordGoesHere';
$database = 'islandRushDB';
@ $db = new mysqli($hostname, $user, $password, $database);
if (mysqli_connect_errno()) {
    echo 'ERROR: Could not connect to database. Error is '.mysqli_connect_error();
    exit;
}
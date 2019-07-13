<?php
//Environment Variables for Database Connection
$hostname = getenv('DB_HOSTNAME');
$user = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_NAME');
@ $db = new mysqli($hostname, $user, $password, $database);
if (mysqli_connect_errno()) {
    echo 'ERROR: Could not connect to database. Error is '.mysqli_connect_error();
    exit;
}
<?php
@ $db = new mysqli('LOCALHOST', 'root', '', 'islandRushDB2');
if (mysqli_connect_errno()) {
    echo 'ERROR: Could not connect to database.  Error is '.mysqli_connect_error();
    exit;
}
<?php
session_start();

$thisPos = (int) $_REQUEST['positionId'];

$array1 = [];
for ($j = 0; $j < sizeof($_SESSION['dist'][0]); $j++) {
    if ($_SESSION['dist'][$thisPos][$j] == 1) {
        array_push($array1, $j);
    }
}

echo json_encode($array1);

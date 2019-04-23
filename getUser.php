<?php

$userId = (int) $_GET['userId'];

$allUsers = array("{id:0, name: 'Spencer'}", "{id:1, name: 'Bailey'}", "{id:2, name: 'Adam'}", "{id:3, name: 'Hayze'}", "{id:4, name: 'Someone'}", "{id:5, name: 'Kenobi'}");

echo $allUsers[$userId];

exit;

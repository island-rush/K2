<?php
session_start();

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$out_container = -1;
$unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];

include("backend/db.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Island Rush Game V2.5</title>
    <link rel="stylesheet" type="text/css" href="frontend/css/game.css">
</head>

<body>
<div id="whole_game">
    <div id="side_panel">
        <div id="titlebar">Logged into: Loading...<br>Reinforcement Shop</div>
        <div id="purchase_buttons_container">
            <div class="purchase_square Transport" title="Transport&#013;Cost: 8&#013;Moves: 2" id="Transport" data-unitId="0"></div>
            <div class="purchase_square Submarine" title="Submarine&#013;Cost: 8&#013;Moves: 2" id="Submarine" data-unitId="1"></div>
            <div class="purchase_square Destroyer" title="Destroyer&#013;Cost: 10&#013;Moves: 2" id="Destroyer" data-unitId="2"></div>
            <div class="purchase_square AircraftCarrier" title="AircraftCarrier&#013;Cost: 15&#013;Moves: 2" id="AircraftCarrier" data-unitId="3"></div>
            <div class="purchase_square ArmyCompany" title="ArmyCompany&#013;Cost: 4&#013;Moves: 1" id="ArmyCompany" data-unitId="4"></div>
            <div class="purchase_square ArtilleryBattery" title="ArtilleryBattery&#013;Cost: 5&#013;Moves: 1" id="ArtilleryBattery" data-unitId="5"></div>
            <div class="purchase_square TankPlatoon" title="TankPlatoon&#013;Cost: 6&#013;Moves: 1" id="TankPlatoon" data-unitId="6"></div>
            <div class="purchase_square MarinePlatoon" title="MarinePlatoon&#013;Cost: 5&#013;Moves: 1" id="MarinePlatoon" data-unitId="7"></div>
            <div class="purchase_square MarineConvoy" title="MarineConvoy&#013;Cost: 8&#013;Moves: 2" id="MarineConvoy" data-unitId="8"></div>
            <div class="purchase_square AttackHelo" title="AttackHelo&#013;Cost: 7&#013;Moves: 3" id="AttackHelo" data-unitId="9"></div>
            <div class="purchase_square SAM" title="SAM&#013;Cost: 8&#013;Moves: 1" id="SAM" data-unitId="10"></div>
            <div class="purchase_square FighterSquadron" title="FighterSquadron&#013;Cost: 12&#013;Moves: 4" id="FighterSquadron" data-unitId="11"></div>
            <div class="purchase_square BomberSquadron" title="BomberSquadron&#013;Cost: 12&#013;Moves: 6" id="BomberSquadron" data-unitId="12"></div>
            <div class="purchase_square StealthBomberSquadron" title="StealthBomberSquadron&#013;Cost: 15&#013;Moves: 5" id="StealthBomberSquadron" data-unitId="13"></div>
            <div class="purchase_square Tanker" title="Tanker&#013;Cost: 11&#013;Moves: 5" id="Tanker" data-unitId="14"></div>
            <div class="purchase_square LandBasedSeaMissile" title="LandBasedSeaMissile&#013;Cost: 10" id="LandBasedSeaMissile" data-unitId="15"></div>
        </div>
        <div id="purchase_seperator">Inventory</div>
        <div id="shopping_things">
            <div id="purchased_container" data-positionType="purchased_container" data-positionId="118"><?php $positionId = 118; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div id="trashbox"></div>
        </div>
        <div id="rest_things">
            <div id="phase_indicator">Current Phase = Loading...</div>
            <div id="team_indicators">
                <div id="red_team_indicator">Züün</div>
                <div id="blue_team_indicator">Vestrland</div>
            </div>
            <div id="rPoints_indicators">
                <div id="red_rPoints_indicator">Loading</div>
                <div id="rPoints_label">RP</div>
                <div id="blue_rPoints_indicator">Loading</div>
            </div>
            <div id="hPoints_indicators">
                <div id="red_hPoints_indicator">Loading</div>
                <div id="hPoints_label">HWP</div>
                <div id="blue_hPoints_indicator">Loading</div>
            </div>
            <div id="misc_info_undo">
                <div id="logout_div">
                    <button id="logout_button">Logout</button>
                </div>
                <div id="undo_button_div">
                    <button id="undo_button" disabled>Undo Movement</button>
                </div>
            </div>
        </div>
        <div id="bottom_panel">
            <div id="battle_button_container">
                <button id="battle_button" disabled>Loading...</button>
            </div>
            <div id="user_feedback_container">
                <div id="user_feedback">User Feedback Loading...</div>
            </div>
            <div id="phase_button_container">
                <button id="phase_button" disabled>Next Phase</button>
            </div>
        </div>
    </div>

    <div id="game_board">
        <div id="grid_marker_top"></div>
        <div class="gridblockLeftBig" title="This island is worth 15 Reinforcement Points" id="special_island13" data-islandNum="13">
            <div class="gridblockTiny" id="pos13a" data-positionId="55"><?php $positionId = 55; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos13b" data-positionId="56"><?php $positionId = 56; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos13c" data-positionId="57"><?php $positionId = 57; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos13d" data-positionId="58"><?php $positionId = 58; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos13e" data-positionId="59"><?php $positionId = 59; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos13f" data-positionId="60"><?php $positionId = 60; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos13g" data-positionId="61"><?php $positionId = 61; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos13h" data-positionId="62"><?php $positionId = 62; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos13i" data-positionId="63"><?php $positionId = 63; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos13j" data-positionId="64"><?php $positionId = 64; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        </div>
        <div class="gridblock water" data-positionId="0"><?php $positionId = 0; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="1"><?php $positionId = 1; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="2"><?php $positionId = 2; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="3"><?php $positionId = 3; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="4"><?php $positionId = 4; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="5"><?php $positionId = 5; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="6"><?php $positionId = 6; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="7"><?php $positionId = 7; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="8"><?php $positionId = 8; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island1" title="This island is worth 4 Reinforcement Points" id="special_island1">
            <div id="special_island1_pop" class="special_island1 special_island3x3" data-islandNum="1">
                <div class="gridblockTiny" id="pos1a" data-positionId="75"><?php $positionId = 75; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos1b" data-positionId="76"><?php $positionId = 76; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos1c" data-positionId="77"><?php $positionId = 77; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos1d" data-positionId="78"><?php $positionId = 78; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="9"><?php $positionId = 9; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="10"><?php $positionId = 10; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island2" title="This island is worth 6 Reinforcement Points" id="special_island2">
            <div id="special_island2_pop" class="special_island2 special_island3x3" data-islandNum="2">
                <div class="gridblockTiny" id="pos2a" data-positionId="79"><?php $positionId = 79; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos2b" data-positionId="80"><?php $positionId = 80; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos2c" data-positionId="81"><?php $positionId = 81; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos2d" data-positionId="82"><?php $positionId = 82; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny missileContainer" id="posM1" data-positionId="121"><?php $positionId = 121; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="11"><?php $positionId = 11; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island3" title="This island is worth 4 Reinforcement Points" id="special_island3">
            <div id="special_island3_pop" class="special_island3 special_island3x3" data-islandNum="3">
                <div class="gridblockTiny" id="pos3a" data-positionId="83"><?php $positionId = 83; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos3b" data-positionId="84"><?php $positionId = 84; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos3c" data-positionId="85"><?php $positionId = 85; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="12"><?php $positionId = 12; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblockRightBig" title="This island is worth 25 Reinforcement Points" id="special_island14" data-islandNum="14">
            <div class="gridblockTiny" id="pos14a" data-positionId="65"><?php $positionId = 65; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos14b" data-positionId="66"><?php $positionId = 66; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos14c" data-positionId="67"><?php $positionId = 67; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos14d" data-positionId="68"><?php $positionId = 68; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos14e" data-positionId="69"><?php $positionId = 69; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos14f" data-positionId="70"><?php $positionId = 70; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos14g" data-positionId="71"><?php $positionId = 71; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos14h" data-positionId="72"><?php $positionId = 72; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos14i" data-positionId="73"><?php $positionId = 73; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div class="gridblockTiny" id="pos14j" data-positionId="74"><?php $positionId = 74; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        </div>
        <div class="gridblock water" data-positionId="13"><?php $positionId = 13; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="14"><?php $positionId = 14; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="15"><?php $positionId = 15; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island4" title="This island is worth 3 Reinforcement Points" id="special_island4">
            <div id="special_island4_pop" class="special_island4 special_island3x3" data-islandNum="4">
                <div class="gridblockTiny" id="pos4a" data-positionId="86"><?php $positionId = 86; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos4b" data-positionId="87"><?php $positionId = 87; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos4c" data-positionId="88"><?php $positionId = 88; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos4d" data-positionId="89"><?php $positionId = 89; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="16"><?php $positionId = 16; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="17"><?php $positionId = 17; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="18"><?php $positionId = 18; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblockEmptyLeft"></div>
        <div class="gridblock water" data-positionId="19"><?php $positionId = 19; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="20"><?php $positionId = 20; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="21"><?php $positionId = 21; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island5_1" title="This island is worth 8 Reinforcement Points" id="special_island5">
            <div id="special_island5_pop" class="special_island5 special_island3x3" data-islandNum="5">
                <div class="gridblockTiny" id="pos5a" data-positionId="90"><?php $positionId = 90; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos5b" data-positionId="91"><?php $positionId = 91; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos5c" data-positionId="92"><?php $positionId = 92; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos5d" data-positionId="93"><?php $positionId = 93; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="22"><?php $positionId = 22; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="23"><?php $positionId = 23; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="24"><?php $positionId = 24; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island6" title="This island is worth 7 Reinforcement Points" id="special_island6">
            <div id="special_island6_pop" class="special_island6 special_island3x3" data-islandNum="6">
                <div class="gridblockTiny" id="pos6a" data-positionId="94"><?php $positionId = 94; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos6b" data-positionId="95"><?php $positionId = 95; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos6c" data-positionId="96"><?php $positionId = 96; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny missileContainer" id="posM2" data-positionId="122"><?php $positionId = 122; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="25"><?php $positionId = 25; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="26"><?php $positionId = 26; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island7" title="This island is worth 7 Reinforcement Points" id="special_island7">
            <div id="special_island7_pop" class="special_island7 special_island3x3" data-islandNum="7">
                <div class="gridblockTiny" id="pos7a" data-positionId="97"><?php $positionId = 97; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos7b" data-positionId="98"><?php $positionId = 98; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos7c" data-positionId="99"><?php $positionId = 99; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny missileContainer" id="posM3" data-positionId="123"><?php $positionId = 123; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="27"><?php $positionId = 27; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island5_2" title="This island is worth 8 Reinforcement Points" id="special_island5_extra">
        </div>
        <div class="gridblock water" data-positionId="28"><?php $positionId = 28; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island8" title="This island is worth 10 Reinforcement Points" id="special_island8">
            <div id="special_island8_pop" class="special_island8 special_island3x3" data-islandNum="8">
                <div class="gridblockTiny" id="pos8a" data-positionId="100"><?php $positionId = 100; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos8b" data-positionId="101"><?php $positionId = 101; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos8c" data-positionId="102"><?php $positionId = 102; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="29"><?php $positionId = 29; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="30"><?php $positionId = 30; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="31"><?php $positionId = 31; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="32"><?php $positionId = 32; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="33"><?php $positionId = 33; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="34"><?php $positionId = 34; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="35"><?php $positionId = 35; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island9" title="This island is worth 8 Reinforcement Points" id="special_island9">
            <div id="special_island9_pop" class="special_island9 special_island3x3" data-islandNum="9">
                <div class="gridblockTiny" id="pos9a" data-positionId="103"><?php $positionId = 103; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos9b" data-positionId="104"><?php $positionId = 104; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos9c" data-positionId="105"><?php $positionId = 105; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos9d" data-positionId="106"><?php $positionId = 106; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny missileContainer" id="posM4" data-positionId="124"><?php $positionId = 124; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="36"><?php $positionId = 36; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="37"><?php $positionId = 37; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island10" title="This island is worth 5 Reinforcement Points" id="special_island10">
            <div id="special_island10_pop" class="special_island10 special_island3x3" data-islandNum="10">
                <div class="gridblockTiny" id="pos10a" data-positionId="107"><?php $positionId = 107; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos10b" data-positionId="108"><?php $positionId = 108; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos10c" data-positionId="109"><?php $positionId = 109; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos10d" data-positionId="110"><?php $positionId = 110; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="38"><?php $positionId = 38; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="39"><?php $positionId = 39; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="40"><?php $positionId = 40; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island11" title="This island is worth 5 Reinforcement Points" id="special_island11">
            <div id="special_island11_pop" class="special_island11 special_island3x3" data-islandNum="11">
                <div class="gridblockTiny" id="pos11a" data-positionId="111"><?php $positionId = 111; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos11b" data-positionId="112"><?php $positionId = 112; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos11c" data-positionId="113"><?php $positionId = 113; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="41"><?php $positionId = 41; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="42"><?php $positionId = 42; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island12" title="This island is worth 5 Reinforcement Points" id="special_island12">
            <div id="special_island12_pop" class="special_island12 special_island3x3" data-islandNum="12">
                <div class="gridblockTiny" id="pos12a" data-positionId="114"><?php $positionId = 114; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos12b" data-positionId="115"><?php $positionId = 115; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos12c" data-positionId="116"><?php $positionId = 116; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny" id="pos12d" data-positionId="117"><?php $positionId = 117; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div class="gridblock water" data-positionId="43"><?php $positionId = 43; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="44"><?php $positionId = 44; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="45"><?php $positionId = 45; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="46"><?php $positionId = 46; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="47"><?php $positionId = 47; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="48"><?php $positionId = 48; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="49"><?php $positionId = 49; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="50"><?php $positionId = 50; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="51"><?php $positionId = 51; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="52"><?php $positionId = 52; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="53"><?php $positionId = 53; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock water" data-positionId="54"><?php $positionId = 54; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="battleZonePopup">
            <div id="unused_attacker" data-boxId="1"><?php $boxId = 1; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
            <div id="unused_defender" data-boxId="2"><?php $boxId = 2; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
            <div id="used_attacker" data-boxId="3"><?php $boxId = 3; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
            <div id="used_defender" data-boxId="4"><?php $boxId = 4; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
            <div id="center_attacker" data-boxId="5"><?php $boxId = 5; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
            <div id="center_defender" data-boxId="6"><?php $boxId = 6; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
            <div id="battle_outcome"></div>
            <div id="battle_buttons">
                <button id="attackButton" disabled>Loading...</button>
                <button id="changeSectionButton" disabled>Loading...</button>
            </div>
            <div id="battleActionPopup">
                <div id="battleActionPopupContainer">
                    <div id="dice_image_container">
                        <div id="dice_image1" class="dice_image"></div>
                        <div id="dice_image2" class="dice_image"></div>
                        <div id="dice_image3" class="dice_image"></div>
                        <div id="dice_image4" class="dice_image"></div>
                        <div id="dice_image5" class="dice_image"></div>
                        <div id="dice_image6" class="dice_image"></div>
                    </div>
                    <div id="lastBattleMessage">Loading...</div>
                    <button id="actionPopupButton">Loading...</button>
                </div>
            </div>
        </div>
        <div id="popup">
            <div id="popupTitle">Loading Title...</div>
            <div id="popupBodyNews">
                <div id="newsBodyText">loading text...</div>
                <div id="newsBodySubText">loading subtext...</div>
            </div>
            <div id="popupBodyHybridMenu">
                <div id="hybridInstructions">
                    <p>Instructions:<br>Select which Hybrid Warfare Option you would like to use. Mouse over the name for more information about what each option does.</p>
                </div>
                <div id="hybridTable">
                    <table>
                        <thead>
                        <tr>
                            <th>Field</th>
                            <th>Name</th>
                            <th>Cost</th>
                            <th>Choose</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td rowspan="2">Cyber</td>
                            <td title="A Cyber attack causes an enemy airfield to be completely shutdown. &#013;Aircraft may not leave or enter that airfield during the enemy turn.">Air Traffic Control Scramble</td>
                            <td>3</td>
                            <td><button id="hybridAirfieldShutdown">Choose</button></td>
                        </tr>
                        <tr>
                            <td title="Enemy island value counts towards your points for the next two turns. &#013;Enemy team does not earn any points from this island.">Bank Drain</td>
                            <td>4</td>
                            <td><button id="hybridBankDrain">Choose</button></td>
                        </tr>
                        <tr>
                            <td rowspan="2">Space</td>
                            <td title="Satellite technology has discovered how to temporarily shorten all &#013;logisical routes. For one turn, all your units get +1 moves.">Advanced Remote Sensing</td>
                            <td>8</td>
                            <td><button id="hybridAddMove">Choose</button></td>
                        </tr>
                        <tr>
                            <td title="Satellite technology allows for kinetic effects from space! &#013;Instantly destroy a unit on the board. &#013;(destroying a container destroys everything inside of it)">Rods from God</td>
                            <td>6</td>
                            <td><button id="hybridDeletePiece">Choose</button></td>
                        </tr>
                        <tr>
                            <td rowspan="2" title="Using a nuclear option makes a team unable to use Humanitarian options for 3 turns">Nuclear*</td>
                            <td title="A high altitude ICBM detonation produces an electromagnetic pulse &#013;over all enemy aircraft, disabling them for their next turn.">Goldeneye</td>
                            <td>10</td>
                            <td><button id="hybridAircraftDisable">Choose</button></td>
                        </tr>
                        <tr>
                            <td title="An ICBM ground burst strike destroys a non-capital island. All units on island &#013;and adjacent sea zones are destroyed. The island will not be used for &#013;the rest of the game and does not contribute to points.">Nuclear Strike</td>
                            <td>12</td>
                            <td><button id="hybridNukeIsland">Choose</button></td>
                        </tr>
                        <tr>
                            <td>Humanitarian</td>
                            <td title="When a News alert notifies a team about a catastrophe in an area, &#013;teams have the option to provide humanitarian aid to that nation. &#013;Spend 3 HW points and receive 10 Reinforcement Points.">Humanitarian Option</td>
                            <td>3</td>
                            <td><button id="hybridHumanitarian">Choose</button></td>
                        </tr>
                        </tbody>

                    </table>
                </div>
                <br>
                <button id="popupHybridClose" style="margin:0 auto; width:30%;">Close this popup</button>
            </div>
        </div>
    </div>
</div>


<script src="frontend/js/game.js"></script>
</body>
</html>
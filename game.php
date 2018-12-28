<?php
session_start();
include("backend/db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];

$query = "SELECT gameIsland1, gameIsland2, gameIsland3, gameIsland4, gameIsland5, gameIsland6, gameIsland7, gameIsland8, gameIsland9, gameIsland10, gameIsland11, gameIsland12, gameIsland13, gameIsland14 FROM GAMES WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gameIsland1 = $r['gameIsland1'];
$gameIsland2 = $r['gameIsland2'];
$gameIsland3 = $r['gameIsland3'];
$gameIsland4 = $r['gameIsland4'];
$gameIsland5 = $r['gameIsland5'];
$gameIsland6 = $r['gameIsland6'];
$gameIsland7 = $r['gameIsland7'];
$gameIsland8 = $r['gameIsland8'];
$gameIsland9 = $r['gameIsland9'];
$gameIsland10 = $r['gameIsland10'];
$gameIsland11 = $r['gameIsland11'];
$gameIsland12 = $r['gameIsland12'];
$gameIsland13 = $r['gameIsland13'];
$gameIsland14 = $r['gameIsland14'];

//TODO: combine if no differences (make water click to close stuff function call elsewhere have its own thing)?
$waterFunctions = 'onclick="waterClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"';
$landFunctions = 'onclick="landClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"';

$gridIslandFunctions = 'onclick="gridIslandClick(event, this);" ondragenter="islandDragenter(event, this);" ondragleave="islandDragleave(event, this);"   ';
$popIslandFunctions = ' ondragenter="popupDragenter(event, this);" ondragleave="popupDragleave(event, this);" ondragover="popupDragover(event, this);"';
$trashBoxFunctions = 'ondragover="positionDragover(event, this);" ondrop="pieceTrash(event, this);"';

$landPositionClass = 'class="gridblockTiny"';
$waterClass = 'class="gridblock water"';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Island Rush Game V2.5</title>
    <link rel="stylesheet" type="text/css" href="frontend/css/game.css">
    <script>
        let gameId = <?php echo $gameId; ?>;
        let myTeam = "<?php echo $myTeam; ?>";
        let lastUpdateId = <?php
            $query8 = "SELECT updateId FROM updates WHERE updateGameId = ? ORDER BY updateId DESC";
            $preparedQuery8 = $db->prepare($query8);
            $preparedQuery8->bind_param("i", $gameId);
            $preparedQuery8->execute();
            $results8 = $preparedQuery8->get_result();
            $num_results8 = $results8->num_rows;
            if ($num_results8 == 0) {
                echo 0;
            } else {
                $r8 = $results8->fetch_assoc();
                echo $r8['updateId'];
            }?>;
    </script>
</head>

<body>
<div id="whole_game">
    <div id="side_panel">
        <div id="titlebar">Logged into: <?php echo $_SESSION['gameSection']." - ".$_SESSION['gameInstructor']." - ".$_SESSION['myTeam']; ?><br>Reinforcement Shop</div>
        <div id="purchase_buttons_container">
            <div class="purchase_square Transport" title="Transport&#013;Cost: 8&#013;Moves: 2" id="Transport" data-unitId="0" onclick="piecePurchase(0);"></div>
            <div class="purchase_square Submarine" title="Submarine&#013;Cost: 8&#013;Moves: 2" id="Submarine" data-unitId="1" onclick="piecePurchase(1);"></div>
            <div class="purchase_square Destroyer" title="Destroyer&#013;Cost: 10&#013;Moves: 2" id="Destroyer" data-unitId="2" onclick="piecePurchase(2);"></div>
            <div class="purchase_square AircraftCarrier" title="AircraftCarrier&#013;Cost: 15&#013;Moves: 2" id="AircraftCarrier" data-unitId="3" onclick="piecePurchase(3);"></div>
            <div class="purchase_square ArmyCompany" title="ArmyCompany&#013;Cost: 4&#013;Moves: 1" id="ArmyCompany" data-unitId="4" onclick="piecePurchase(4);"></div>
            <div class="purchase_square ArtilleryBattery" title="ArtilleryBattery&#013;Cost: 5&#013;Moves: 1" id="ArtilleryBattery" data-unitId="5" onclick="piecePurchase(5);"></div>
            <div class="purchase_square TankPlatoon" title="TankPlatoon&#013;Cost: 6&#013;Moves: 1" id="TankPlatoon" data-unitId="6" onclick="piecePurchase(6);"></div>
            <div class="purchase_square MarinePlatoon" title="MarinePlatoon&#013;Cost: 5&#013;Moves: 1" id="MarinePlatoon" data-unitId="7" onclick="piecePurchase(7);"></div>
            <div class="purchase_square MarineConvoy" title="MarineConvoy&#013;Cost: 8&#013;Moves: 2" id="MarineConvoy" data-unitId="8" onclick="piecePurchase(8);"></div>
            <div class="purchase_square AttackHelo" title="AttackHelo&#013;Cost: 7&#013;Moves: 3" id="AttackHelo" data-unitId="9" onclick="piecePurchase(9);"></div>
            <div class="purchase_square SAM" title="SAM&#013;Cost: 8&#013;Moves: 1" id="SAM" data-unitId="10" onclick="piecePurchase(10);"></div>
            <div class="purchase_square FighterSquadron" title="FighterSquadron&#013;Cost: 12&#013;Moves: 4" id="FighterSquadron" data-unitId="11" onclick="piecePurchase(11);"></div>
            <div class="purchase_square BomberSquadron" title="BomberSquadron&#013;Cost: 12&#013;Moves: 6" id="BomberSquadron" data-unitId="12" onclick="piecePurchase(12);"></div>
            <div class="purchase_square StealthBomberSquadron" title="StealthBomberSquadron&#013;Cost: 15&#013;Moves: 5" id="StealthBomberSquadron" data-unitId="13" onclick="piecePurchase(13);"></div>
            <div class="purchase_square Tanker" title="Tanker&#013;Cost: 11&#013;Moves: 5" id="Tanker" data-unitId="14" onclick="piecePurchase(14);"></div>
            <div class="purchase_square LandBasedSeaMissile" title="LandBasedSeaMissile&#013;Cost: 10" id="LandBasedSeaMissile" data-unitId="15" onclick="piecePurchase(15);"></div>
        </div>
        <div id="purchase_seperator">Inventory</div>
        <div id="shopping_things">
            <div id="purchased_container" data-positionType="purchased_container" data-positionId="118"><?php $positionId = 118; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div id="trashbox" <?php echo $trashBoxFunctions; ?>></div>
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
                    <button id="logout_button" onclick="logout();">Logout</button>
                </div>
                <div id="undo_button_div">
                    <button id="undo_button" disabled onclick="undoButtonFunction();">Undo Movement</button>
                </div>
            </div>
        </div>
        <div id="bottom_panel">
            <div id="battle_button_container">
                <button id="control_button" disabled onclick="controlButtonFunction();">Loading...</button>
            </div>
            <div id="user_feedback_container">
                <div id="user_feedback">User Feedback</div>
            </div>
            <div id="phase_button_container">
                <button id="phase_button" class="<?php echo 'phase_'.$_SESSION['myTeam']; ?>" disabled onclick="nextPhaseButtonFunction();">Next Phase</button>
            </div>
        </div>
    </div>
    <div id="game_board" data-placementId="-1">
        <div id="grid_marker_top"></div>
        <div id="special_island13" class="gridblockLeftBig <?php echo $gameIsland13; ?>" title="This island is worth 15 Reinforcement Points" data-islandNum="13" data-placementId="-1">
            <div <?php echo $landPositionClass; ?> id="pos13a" data-positionId="55" <?php echo $landFunctions; ?>><?php $positionId = 55; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos13b" data-positionId="56" <?php echo $landFunctions; ?>><?php $positionId = 56; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos13c" data-positionId="57" <?php echo $landFunctions; ?>><?php $positionId = 57; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos13d" data-positionId="58" <?php echo $landFunctions; ?>><?php $positionId = 58; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos13e" data-positionId="59" <?php echo $landFunctions; ?>><?php $positionId = 59; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos13f" data-positionId="60" <?php echo $landFunctions; ?>><?php $positionId = 60; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos13g" data-positionId="61" <?php echo $landFunctions; ?>><?php $positionId = 61; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos13h" data-positionId="62" <?php echo $landFunctions; ?>><?php $positionId = 62; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos13i" data-positionId="63" <?php echo $landFunctions; ?>><?php $positionId = 63; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos13j" data-positionId="64" <?php echo $landFunctions; ?>><?php $positionId = 64; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="0" <?php echo $waterFunctions; ?>><?php $positionId = 0; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="1" <?php echo $waterFunctions; ?>><?php $positionId = 1; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="2" <?php echo $waterFunctions; ?>><?php $positionId = 2; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="3" <?php echo $waterFunctions; ?>><?php $positionId = 3; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="4" <?php echo $waterFunctions; ?>><?php $positionId = 4; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="5" <?php echo $waterFunctions; ?>><?php $positionId = 5; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="6" <?php echo $waterFunctions; ?>><?php $positionId = 6; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="7" <?php echo $waterFunctions; ?>><?php $positionId = 7; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="8" <?php echo $waterFunctions; ?>><?php $positionId = 8; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="special_island1" class="gridblock grid_special_island1 <?php echo $gameIsland1; ?>" data-islandNum="1" title="This island is worth 4 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
            <div id="special_island1_pop" class="special_island1 special_island3x3 <?php echo $gameIsland1; ?>" data-islandNum="1" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                <div <?php echo $landPositionClass; ?> id="pos1a" data-positionId="75" <?php echo $landFunctions; ?>><?php $positionId = 75; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos1b" data-positionId="76" <?php echo $landFunctions; ?>><?php $positionId = 76; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos1c" data-positionId="77" <?php echo $landFunctions; ?>><?php $positionId = 77; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos1d" data-positionId="78" <?php echo $landFunctions; ?>><?php $positionId = 78; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="9" <?php echo $waterFunctions; ?>><?php $positionId = 9; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="10" <?php echo $waterFunctions; ?>><?php $positionId = 10; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="special_island2" class="gridblock grid_special_island2 <?php echo $gameIsland2; ?>" data-islandNum="2" title="This island is worth 6 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
            <div id="special_island2_pop" class="special_island2 special_island3x3 <?php echo $gameIsland2; ?>" data-islandNum="2" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                <div <?php echo $landPositionClass; ?> id="pos2a" data-positionId="79" <?php echo $landFunctions; ?>><?php $positionId = 79; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos2b" data-positionId="80" <?php echo $landFunctions; ?>><?php $positionId = 80; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos2c" data-positionId="81" <?php echo $landFunctions; ?>><?php $positionId = 81; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos2d" data-positionId="82" <?php echo $landFunctions; ?>><?php $positionId = 82; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny missileContainer" id="posM1" data-positionId="121"><?php $positionId = 121; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="11" <?php echo $waterFunctions; ?>><?php $positionId = 11; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="special_island3" class="gridblock grid_special_island3 <?php echo $gameIsland3; ?>" data-islandNum="3" title="This island is worth 4 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
            <div id="special_island3_pop" class="special_island3 special_island3x3 <?php echo $gameIsland3; ?>" data-islandNum="3" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                <div <?php echo $landPositionClass; ?> id="pos3a" data-positionId="83" <?php echo $landFunctions; ?>><?php $positionId = 83; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos3b" data-positionId="84" <?php echo $landFunctions; ?>><?php $positionId = 84; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos3c" data-positionId="85" <?php echo $landFunctions; ?>><?php $positionId = 85; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="12" <?php echo $waterFunctions; ?>><?php $positionId = 12; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="special_island14" class="gridblockRightBig <?php echo $gameIsland14; ?>" title="This island is worth 25 Reinforcement Points" data-islandNum="14" data-placementId="-1">
            <div <?php echo $landPositionClass; ?> id="pos14a" data-positionId="65" <?php echo $landFunctions; ?>><?php $positionId = 65; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos14b" data-positionId="66" <?php echo $landFunctions; ?>><?php $positionId = 66; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos14c" data-positionId="67" <?php echo $landFunctions; ?>><?php $positionId = 67; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos14d" data-positionId="68" <?php echo $landFunctions; ?>><?php $positionId = 68; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos14e" data-positionId="69" <?php echo $landFunctions; ?>><?php $positionId = 69; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos14f" data-positionId="70" <?php echo $landFunctions; ?>><?php $positionId = 70; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos14g" data-positionId="71" <?php echo $landFunctions; ?>><?php $positionId = 71; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos14h" data-positionId="72" <?php echo $landFunctions; ?>><?php $positionId = 72; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos14i" data-positionId="73" <?php echo $landFunctions; ?>><?php $positionId = 73; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            <div <?php echo $landPositionClass; ?> id="pos14j" data-positionId="74" <?php echo $landFunctions; ?>><?php $positionId = 74; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="13" <?php echo $waterFunctions; ?>><?php $positionId = 13; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="14" <?php echo $waterFunctions; ?>><?php $positionId = 14; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="15" <?php echo $waterFunctions; ?>><?php $positionId = 15; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="special_island4" class="gridblock grid_special_island4 <?php echo $gameIsland4; ?>" data-islandNum="4" title="This island is worth 3 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
            <div id="special_island4_pop" class="special_island4 special_island3x3 <?php echo $gameIsland4; ?>" data-islandNum="4" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                <div <?php echo $landPositionClass; ?> id="pos4a" data-positionId="86" <?php echo $landFunctions; ?>><?php $positionId = 86; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos4b" data-positionId="87" <?php echo $landFunctions; ?>><?php $positionId = 87; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos4c" data-positionId="88" <?php echo $landFunctions; ?>><?php $positionId = 88; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos4d" data-positionId="89" <?php echo $landFunctions; ?>><?php $positionId = 89; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="16" <?php echo $waterFunctions; ?>><?php $positionId = 16; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="17" <?php echo $waterFunctions; ?>><?php $positionId = 17; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="18" <?php echo $waterFunctions; ?>><?php $positionId = 18; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblockEmptyLeft"></div>
        <div <?php echo $waterClass; ?> data-positionId="19" <?php echo $waterFunctions; ?>><?php $positionId = 19; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="20" <?php echo $waterFunctions; ?>><?php $positionId = 20; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="21" <?php echo $waterFunctions; ?>><?php $positionId = 21; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="special_island5" class="gridblock grid_special_island5_1 <?php echo $gameIsland5; ?>" data-islandNum="5" title="This island is worth 8 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
            <div id="special_island5_pop" class="special_island5 special_island3x3 <?php echo $gameIsland5; ?>" data-islandNum="5" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                <div <?php echo $landPositionClass; ?> id="pos5a" data-positionId="90" <?php echo $landFunctions; ?>><?php $positionId = 90; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos5b" data-positionId="91" <?php echo $landFunctions; ?>><?php $positionId = 91; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos5c" data-positionId="92" <?php echo $landFunctions; ?>><?php $positionId = 92; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos5d" data-positionId="93" <?php echo $landFunctions; ?>><?php $positionId = 93; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="22" <?php echo $waterFunctions; ?>><?php $positionId = 22; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="23" <?php echo $waterFunctions; ?>><?php $positionId = 23; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="24" <?php echo $waterFunctions; ?>><?php $positionId = 24; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="special_island6" class="gridblock grid_special_island6 <?php echo $gameIsland6; ?>" data-islandNum="6" title="This island is worth 7 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
            <div id="special_island6_pop" class="special_island6 special_island3x3 <?php echo $gameIsland6; ?>" data-islandNum="6" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                <div <?php echo $landPositionClass; ?> id="pos6a" data-positionId="94" <?php echo $landFunctions; ?>><?php $positionId = 94; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos6b" data-positionId="95" <?php echo $landFunctions; ?>><?php $positionId = 95; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos6c" data-positionId="96" <?php echo $landFunctions; ?>><?php $positionId = 96; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny missileContainer" id="posM2" data-positionId="122"><?php $positionId = 122; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="25" <?php echo $waterFunctions; ?>><?php $positionId = 25; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="26" <?php echo $waterFunctions; ?>><?php $positionId = 26; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="special_island7" class="gridblock grid_special_island7 <?php echo $gameIsland7; ?>" data-islandNum="7" title="This island is worth 7 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
            <div id="special_island7_pop" class="special_island7 special_island3x3 <?php echo $gameIsland7; ?>" data-islandNum="7" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                <div <?php echo $landPositionClass; ?> id="pos7a" data-positionId="97" <?php echo $landFunctions; ?>><?php $positionId = 97; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos7b" data-positionId="98" <?php echo $landFunctions; ?>><?php $positionId = 98; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos7c" data-positionId="99" <?php echo $landFunctions; ?>><?php $positionId = 99; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny missileContainer" id="posM3" data-positionId="123"><?php $positionId = 123; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="27" <?php echo $waterFunctions; ?>><?php $positionId = 27; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div class="gridblock grid_special_island5_2  <?php echo $gameIsland5; ?>" title="This island is worth 8 Reinforcement Points" data-islandNum="5" id="special_island5_extra" <?php echo $gridIslandFunctions; ?>>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="28" <?php echo $waterFunctions; ?>><?php $positionId = 28; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="special_island8" class="gridblock grid_special_island8 <?php echo $gameIsland8; ?>" data-islandNum="8" title="This island is worth 10 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
            <div id="special_island8_pop" class="special_island8 special_island3x3 <?php echo $gameIsland8; ?>" data-islandNum="8" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                <div <?php echo $landPositionClass; ?> id="pos8a" data-positionId="100" <?php echo $landFunctions; ?>><?php $positionId = 100; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos8b" data-positionId="101" <?php echo $landFunctions; ?>><?php $positionId = 101; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos8c" data-positionId="102" <?php echo $landFunctions; ?>><?php $positionId = 102; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="29" <?php echo $waterFunctions; ?>><?php $positionId = 29; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="30" <?php echo $waterFunctions; ?>><?php $positionId = 30; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="31" <?php echo $waterFunctions; ?>><?php $positionId = 31; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="32" <?php echo $waterFunctions; ?>><?php $positionId = 32; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="33" <?php echo $waterFunctions; ?>><?php $positionId = 33; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="34" <?php echo $waterFunctions; ?>><?php $positionId = 34; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="35" <?php echo $waterFunctions; ?>><?php $positionId = 35; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="special_island9" class="gridblock grid_special_island9 <?php echo $gameIsland9; ?>" data-islandNum="9" title="This island is worth 8 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
            <div id="special_island9_pop" class="special_island9 special_island3x3 <?php echo $gameIsland9; ?>" data-islandNum="9" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                <div <?php echo $landPositionClass; ?> id="pos9a" data-positionId="103" <?php echo $landFunctions; ?>><?php $positionId = 103; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos9b" data-positionId="104" <?php echo $landFunctions; ?>><?php $positionId = 104; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos9c" data-positionId="105" <?php echo $landFunctions; ?>><?php $positionId = 105; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos9d" data-positionId="106" <?php echo $landFunctions; ?>><?php $positionId = 106; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div class="gridblockTiny missileContainer" id="posM4" data-positionId="124"><?php $positionId = 124; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="36" <?php echo $waterFunctions; ?>><?php $positionId = 36; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="37" <?php echo $waterFunctions; ?>><?php $positionId = 37; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="special_island10" class="gridblock grid_special_island10 <?php echo $gameIsland10; ?>" data-islandNum="10" title="This island is worth 5 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
            <div id="special_island10_pop" class="special_island10 special_island3x3 <?php echo $gameIsland10; ?>" data-islandNum="10" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                <div <?php echo $landPositionClass; ?> id="pos10a" data-positionId="107" <?php echo $landFunctions; ?>><?php $positionId = 107; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos10b" data-positionId="108" <?php echo $landFunctions; ?>><?php $positionId = 108; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos10c" data-positionId="109" <?php echo $landFunctions; ?>><?php $positionId = 109; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos10d" data-positionId="110" <?php echo $landFunctions; ?>><?php $positionId = 110; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="38" <?php echo $waterFunctions; ?>><?php $positionId = 38; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="39" <?php echo $waterFunctions; ?>><?php $positionId = 39; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="40" <?php echo $waterFunctions; ?>><?php $positionId = 40; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="special_island11" class="gridblock grid_special_island11 <?php echo $gameIsland11; ?>" data-islandNum="11" title="This island is worth 5 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
            <div id="special_island11_pop" class="special_island11 special_island3x3 <?php echo $gameIsland11; ?>" data-islandNum="11" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                <div <?php echo $landPositionClass; ?> id="pos11a" data-positionId="111" <?php echo $landFunctions; ?>><?php $positionId = 111; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos11b" data-positionId="112" <?php echo $landFunctions; ?>><?php $positionId = 112; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos11c" data-positionId="113" <?php echo $landFunctions; ?>><?php $positionId = 113; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="41" <?php echo $waterFunctions; ?>><?php $positionId = 41; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="42" <?php echo $waterFunctions; ?>><?php $positionId = 42; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="special_island12" class="gridblock grid_special_island12 <?php echo $gameIsland12; ?>" data-islandNum="12" title="This island is worth 5 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
            <div id="special_island12_pop" class="special_island12 special_island3x3 <?php echo $gameIsland12; ?>" data-islandNum="12" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                <div <?php echo $landPositionClass; ?> id="pos12a" data-positionId="114" <?php echo $landFunctions; ?>><?php $positionId = 114; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos12b" data-positionId="115" <?php echo $landFunctions; ?>><?php $positionId = 115; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos12c" data-positionId="116" <?php echo $landFunctions; ?>><?php $positionId = 116; include("backend/game/pieces/pieceDisplay.php"); ?></div>
                <div <?php echo $landPositionClass; ?> id="pos12d" data-positionId="117" <?php echo $landFunctions; ?>><?php $positionId = 117; include("backend/game/pieces/pieceDisplay.php"); ?></div>
            </div>
        </div>
        <div <?php echo $waterClass; ?> data-positionId="43" <?php echo $waterFunctions; ?>><?php $positionId = 43; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="44" <?php echo $waterFunctions; ?>><?php $positionId = 44; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="45" <?php echo $waterFunctions; ?>><?php $positionId = 45; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="46" <?php echo $waterFunctions; ?>><?php $positionId = 46; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="47" <?php echo $waterFunctions; ?>><?php $positionId = 47; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="48" <?php echo $waterFunctions; ?>><?php $positionId = 48; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="49" <?php echo $waterFunctions; ?>><?php $positionId = 49; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="50" <?php echo $waterFunctions; ?>><?php $positionId = 50; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="51" <?php echo $waterFunctions; ?>><?php $positionId = 51; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="52" <?php echo $waterFunctions; ?>><?php $positionId = 52; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="53" <?php echo $waterFunctions; ?>><?php $positionId = 53; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div <?php echo $waterClass; ?> data-positionId="54" <?php echo $waterFunctions; ?>><?php $positionId = 54; include("backend/game/pieces/pieceDisplay.php"); ?></div>
        <div id="battleZonePopup">
            <div id="unused_attacker" data-boxId="1"><?php $boxId = 1; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
            <div id="unused_defender" data-boxId="2"><?php $boxId = 2; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
            <div id="used_attacker" data-boxId="3"><?php $boxId = 5; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
            <div id="used_defender" data-boxId="4"><?php $boxId = 6; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
            <div id="center_attacker" data-boxId="5"><?php $boxId = 3; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
            <div id="center_defender" data-boxId="6"><?php $boxId = 4; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
            <div id="battle_outcome"></div>
            <div id="battle_buttons">
                <button id="attackButton" disabled onclick="attackButtonFunction();">Loading...</button>
                <button id="changeSectionButton" disabled onclick="changeSectionButtonFunction();">Loading...</button>
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
                    <button id="actionPopupButton" disabled onclick="battleActionPopupButtonClick();">Loading...</button>
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
                            <td><button id="hybridAirfieldShutdown" onclick="hybridDisableAirfield();">Choose</button></td>
                        </tr>
                        <tr>
                            <td title="Enemy island value counts towards your points for the next two turns. &#013;Enemy team does not earn any points from this island.">Bank Drain</td>
                            <td>4</td>
                            <td><button id="hybridBankDrain" onclick="hybridBankDrain();">Choose</button></td>
                        </tr>
                        <tr>
                            <td rowspan="2">Space</td>
                            <td title="Satellite technology has discovered how to temporarily shorten all &#013;logisical routes. For one turn, all your units get +1 moves.">Advanced Remote Sensing</td>
                            <td>8</td>
                            <td><button id="hybridAddMove" onclick="hybridAddMove();">Choose</button></td>
                        </tr>
                        <tr>
                            <td title="Satellite technology allows for kinetic effects from space! &#013;Instantly destroy a unit on the board. &#013;(destroying a container destroys everything inside of it)">Rods from God</td>
                            <td>6</td>
                            <td><button id="hybridDeletePiece" onclick="hybridDeletePiece();">Choose</button></td>
                        </tr>
                        <tr>
                            <td rowspan="2" title="Using a nuclear option makes a team unable to use Humanitarian options for 3 turns">Nuclear*</td>
                            <td title="A high altitude ICBM detonation produces an electromagnetic pulse &#013;over all enemy aircraft, disabling them for their next turn.">Goldeneye</td>
                            <td>10</td>
                            <td><button id="hybridAircraftDisable" onclick="hybridDisableAircraft();">Choose</button></td>
                        </tr>
                        <tr>
                            <td title="An ICBM ground burst strike destroys a non-capital island. All units on island &#013;and adjacent sea zones are destroyed. The island will not be used for &#013;the rest of the game and does not contribute to points.">Nuclear Strike</td>
                            <td>12</td>
                            <td><button id="hybridNukeIsland" onclick="hybridNuke();">Choose</button></td>
                        </tr>
                        <tr>
                            <td>Humanitarian</td>
                            <td title="When a News alert notifies a team about a catastrophe in an area, &#013;teams have the option to provide humanitarian aid to that nation. &#013;Spend 3 HW points and receive 10 Reinforcement Points.">Humanitarian Option</td>
                            <td>3</td>
                            <td><button id="hybridHumanitarian" onclick="hybridHumanitarian();">Choose</button></td>
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
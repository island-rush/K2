<?php
session_start();
include("backend/db.php");
if (!isset($_SESSION['gameId']) && !isset($_SESSION['myTeam'])) {
    header("location:index.php?err=9");
    exit;
}
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
$unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
$query = "SELECT gameActive, gameIsland1, gameIsland2, gameIsland3, gameIsland4, gameIsland5, gameIsland6, gameIsland7, gameIsland8, gameIsland9, gameIsland10, gameIsland11, gameIsland12, gameIsland13, gameIsland14 FROM games WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gameActive = $r['gameActive'];
if ($gameActive != 1 && $myTeam != "Spec") {
    header("location:index.php?err=1");
    exit;
}
$gameIsland1 = htmlentities($r['gameIsland1']);
$gameIsland2 = htmlentities($r['gameIsland2']);
$gameIsland3 = htmlentities($r['gameIsland3']);
$gameIsland4 = htmlentities($r['gameIsland4']);
$gameIsland5 = htmlentities($r['gameIsland5']);
$gameIsland6 = htmlentities($r['gameIsland6']);
$gameIsland7 = htmlentities($r['gameIsland7']);
$gameIsland8 = htmlentities($r['gameIsland8']);
$gameIsland9 = htmlentities($r['gameIsland9']);
$gameIsland10 = htmlentities($r['gameIsland10']);
$gameIsland11 = htmlentities($r['gameIsland11']);
$gameIsland12 = htmlentities($r['gameIsland12']);
$gameIsland13 = htmlentities($r['gameIsland13']);
$gameIsland14 = htmlentities($r['gameIsland14']);
$waterFunctions = 'draggable="false" ondragstart="event.preventDefault();" onclick="waterClick(event, this);" ondblclick="doubleClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"';
$landFunctions = 'draggable="false" ondragstart="event.preventDefault();" onclick="landClick(event, this);" ondblclick="doubleClick(event, this);" ondragover="positionDragover(event, this);" ondrop="positionDrop(event, this);"';
$gridIslandFunctions = 'draggable="false" ondragstart="event.preventDefault();" onclick="gridIslandClick(event, this);" ondragenter="islandDragenter(event, this);" ondragleave="popupDragenter(event, this);"   ';
$popIslandFunctions = 'draggable="false" ondragstart="event.preventDefault();" ondragenter="popupDragenter(event, this);" ondragleave="popupDragleave(event, this);" ondragover="popupDragenter(event, this);"';
$trashBoxFunctions = 'draggable="false" ondragstart="event.preventDefault();" ondragover="positionDragover(event, this);" ondrop="pieceTrash(event, this);"';
$landPositionClass = 'class="gridblockTiny"';
$waterClass = 'class="gridblock water"';
$pieceFunctions = ' draggable="true" ondragstart="pieceDragstart(event, this);" ondragleave="pieceDragleave(event, this);" onclick="pieceClick(event, this);" ondragenter="pieceDragenter(event, this);" ';
$containerFunctions = " ondragenter='containerDragenter(event, this);' ondragleave='containerDragleave(event, this);' ondragover='positionDragover(event, this);' ondrop='positionDrop(event, this);' ";
$allPiecesArray = array();  //store the top level pieces
$allPiecesContained = array();  //store the contained pieces to be inserted later
$query = 'SELECT placementId, placementUnitId, placementTeamId, placementCurrentMoves, placementBattleUsed, placementContainerId, placementPositionId FROM placements WHERE placementGameId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $gameId);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
for ($i = 0; $i < $num_results; $i++) {  //for each piece that is in the game
    $r = $results->fetch_assoc();
    $thisPieceArray = array(
        'placementId' => (int) $r['placementId'], 
        'placementUnitId' => (int) $r['placementUnitId'], 
        'placementTeamId' => $r['placementTeamId'], 
        'placementCurrentMoves' => (int) $r['placementCurrentMoves'], 
        'placementBattleUsed' => $r['placementBattleUsed'],
        'placementContainerId' => $r['placementContainerId'],
        'placementPositionId' => $r['placementPositionId']
    );
    if ($thisPieceArray['placementContainerId'] != -1) {
        $battleUsedText = "";
        if ($thisPieceArray['placementBattleUsed'] == 1) {
            $battleUsedText = "\nUsed in Attack";
        }
        $thisContainedHTML = "<div class='".$unitNames[$thisPieceArray['placementUnitId']]." gamePiece ".$thisPieceArray['placementTeamId']."' title='".$unitNames[$thisPieceArray['placementUnitId']]."\nMoves: ".$thisPieceArray['placementCurrentMoves'].$battleUsedText."' data-placementId='".$thisPieceArray['placementId']."' ".$pieceFunctions."></div>";
        if (!array_key_exists($thisPieceArray['placementContainerId'], $allPiecesContained)) {
            $allPiecesContained[$thisPieceArray['placementContainerId']] = $thisContainedHTML;
        } else {
            $allPiecesContained[$thisPieceArray['placementContainerId']] = $allPiecesContained[$thisPieceArray['placementContainerId']].$thisContainedHTML;
        }
    } else {
        array_push($allPiecesArray, $thisPieceArray);
    }
}
$finalPieceHTML = array();
for ($z = 0; $z <= 118; $z++) {
    array_push($finalPieceHTML, "");
}
for ($x = 0; $x < sizeof($allPiecesArray); $x++) {
    $thisPiece = $allPiecesArray[$x];  //pieces in here are top level
    $battleUsedText = "";
    if ($thisPiece['placementBattleUsed'] == 1) {
        $battleUsedText = "\nUsed in Attack";
    }
    $thisPieceHTML = "<div class='".$unitNames[$thisPiece['placementUnitId']]." gamePiece ".$thisPiece['placementTeamId']."' title='".$unitNames[$thisPiece['placementUnitId']]."\nMoves: ".$thisPiece['placementCurrentMoves'].$battleUsedText."' data-placementId='".$thisPiece['placementId']."' ".$pieceFunctions.">";
    if ($thisPiece['placementUnitId'] == 0 || $thisPiece['placementUnitId'] == 3) {
        if ($thisPiece['placementUnitId'] == 0) {
            $classthing = "transportContainer";
        } else {
            $classthing = "aircraftCarrierContainer";
        }
        $thisPieceHTML = $thisPieceHTML."<div class='".$classthing."' data-positionId='-1' ".$containerFunctions.">";  //open the container
        if (array_key_exists($thisPiece['placementId'], $allPiecesContained)) {
            $thisPieceHTML = $thisPieceHTML.$allPiecesContained[$thisPiece['placementId']];  //add the contained pieces
        }
        $thisPieceHTML = $thisPieceHTML."</div>";  //end the container
    }
    $thisPieceHTML = $thisPieceHTML."</div>";  //end the overall piece
    $finalPieceHTML[$thisPiece['placementPositionId']] = $finalPieceHTML[$thisPiece['placementPositionId']].$thisPieceHTML;
}

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
                    echo htmlentities($r8['updateId']);
                }?>;
        </script>
    </head>
    <body>
        <div id="whole_game">
            <div id="side_panel">
                <div id="titlebar">Logged into: <?php echo htmlentities($_SESSION['gameSection'])." - ".htmlentities($_SESSION['gameInstructor'])." - ".htmlentities($_SESSION['myTeam']); ?><br>Reinforcement Shop</div>
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
                    <div id="purchased_container" data-positionId="118"><?php echo $finalPieceHTML[118]; ?></div>
                    <div id="trashbox" <?php echo $trashBoxFunctions; ?>></div>
                </div>
                <div id="rest_things">
                    <div id="phase_indicator">Current Phase = Loading...</div>
                    <div id="team_indicators">
                        <div id="red_team_indicator" style="color: red;">Zuun</div>
                        <div id="blue_team_indicator" style="color: blue;">Vestrland</div>
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
                            <button id="logout_button" onclick="logout(false);">Logout</button>
                        </div>
                        <div id="undo_button_div">
                            <button id="undo_button" disabled onclick="generalBackendRequest('backend/game/pieces/pieceMoveUndo.php');">Undo Movement</button>
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
                        <button id="phase_button" class="<?php echo 'phase_'.htmlentities($_SESSION['myTeam']); ?>" disabled onclick="nextPhaseButtonFunction();">Next Phase</button>
                    </div>
                </div>
            </div>
            <div id="game_board" data-placementId="-1">
                <div id="grid_marker_top"></div>
                <div id="special_island13" class="gridblockLeftBig <?php echo $gameIsland13; ?>" title="This island is worth 15 Reinforcement Points" data-islandNum="13" data-placementId="-1">
                    <div <?php echo $landPositionClass; ?> id="pos13a" data-positionId="55" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[55]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos13b" data-positionId="56" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[56]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos13c" data-positionId="57" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[57]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos13d" data-positionId="58" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[58]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos13e" data-positionId="59" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[59]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos13f" data-positionId="60" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[60]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos13g" data-positionId="61" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[61]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos13h" data-positionId="62" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[62]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos13i" data-positionId="63" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[63]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos13j" data-positionId="64" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[64]; ?></div>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="0" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[0]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="1" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[1]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="2" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[2]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="3" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[3]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="4" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[4]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="5" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[5]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="6" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[6]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="7" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[7]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="8" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[8]; ?></div>
                <div id="special_island1" class="gridblock grid_special_island1 <?php echo $gameIsland1; ?>" data-islandNum="1" title="This island is worth 4 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
                    <div id="special_island1_pop" class="special_island1 special_island3x3 <?php echo $gameIsland1; ?>" data-islandNum="1" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                        <div <?php echo $landPositionClass; ?> id="pos1a" data-positionId="75" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[75]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos1b" data-positionId="76" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[76]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos1c" data-positionId="77" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[77]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos1d" data-positionId="78" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[78]; ?></div>
                    </div>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="9" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[9]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="10" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[10]; ?></div>
                <div id="special_island2" class="gridblock grid_special_island2 <?php echo $gameIsland2; ?>" data-islandNum="2" title="This island is worth 6 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
                    <div id="special_island2_pop" class="special_island2 special_island3x3 <?php echo $gameIsland2; ?>" data-islandNum="2" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                        <div <?php echo $landPositionClass; ?> id="pos2a" data-positionId="79" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[79]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos2b" data-positionId="80" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[80]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos2c" data-positionId="81" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[81]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos2d" data-positionId="82" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[82]; ?></div>
                        <div class="gridblockTiny missileContainer" id="posM1" data-positionId="121" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[121]; ?></div>
                    </div>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="11" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[11]; ?></div>
                <div id="special_island3" class="gridblock grid_special_island3 <?php echo $gameIsland3; ?>" data-islandNum="3" title="This island is worth 4 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
                    <div id="special_island3_pop" class="special_island3 special_island3x3 <?php echo $gameIsland3; ?>" data-islandNum="3" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                        <div <?php echo $landPositionClass; ?> id="pos3a" data-positionId="83" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[83]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos3b" data-positionId="84" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[84]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos3c" data-positionId="85" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[85]; ?></div>
                    </div>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="12" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[12]; ?></div>
                <div id="special_island14" class="gridblockRightBig <?php echo $gameIsland14; ?>" title="This island is worth 25 Reinforcement Points" data-islandNum="14" data-placementId="-1">
                    <div <?php echo $landPositionClass; ?> id="pos14a" data-positionId="65" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[65]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos14b" data-positionId="66" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[66]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos14c" data-positionId="67" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[67]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos14d" data-positionId="68" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[68]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos14e" data-positionId="69" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[69]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos14f" data-positionId="70" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[70]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos14g" data-positionId="71" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[71]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos14h" data-positionId="72" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[72]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos14i" data-positionId="73" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[73]; ?></div>
                    <div <?php echo $landPositionClass; ?> id="pos14j" data-positionId="74" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[74]; ?></div>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="13" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[13]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="14" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[14]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="15" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[15]; ?></div>
                <div id="special_island4" class="gridblock grid_special_island4 <?php echo $gameIsland4; ?>" data-islandNum="4" title="This island is worth 3 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
                    <div id="special_island4_pop" class="special_island4 special_island3x3 <?php echo $gameIsland4; ?>" data-islandNum="4" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                        <div <?php echo $landPositionClass; ?> id="pos4a" data-positionId="86" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[86]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos4b" data-positionId="87" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[87]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos4c" data-positionId="88" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[88]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos4d" data-positionId="89" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[89]; ?></div>
                    </div>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="16" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[16]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="17" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[17]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="18" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[18]; ?></div>
                <div class="gridblockEmptyLeft"></div>
                <div <?php echo $waterClass; ?> data-positionId="19" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[19]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="20" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[20]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="21" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[21]; ?></div>
                <div id="special_island5" class="gridblock grid_special_island5_1 <?php echo $gameIsland5; ?>" data-islandNum="5" title="This island is worth 8 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
                    <div id="special_island5_pop" class="special_island5 special_island3x3 <?php echo $gameIsland5; ?>" data-islandNum="5" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                        <div <?php echo $landPositionClass; ?> id="pos5a" data-positionId="90" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[90]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos5b" data-positionId="91" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[91]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos5c" data-positionId="92" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[92]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos5d" data-positionId="93" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[93]; ?></div>
                    </div>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="22" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[22]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="23" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[23]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="24" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[24]; ?></div>
                <div id="special_island6" class="gridblock grid_special_island6 <?php echo $gameIsland6; ?>" data-islandNum="6" title="This island is worth 7 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
                    <div id="special_island6_pop" class="special_island6 special_island3x3 <?php echo $gameIsland6; ?>" data-islandNum="6" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                        <div <?php echo $landPositionClass; ?> id="pos6a" data-positionId="94" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[94]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos6b" data-positionId="95" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[95]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos6c" data-positionId="96" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[96]; ?></div>
                        <div class="gridblockTiny missileContainer" id="posM2" data-positionId="122" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[122]; ?></div>
                    </div>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="25" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[25]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="26" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[26]; ?></div>
                <div id="special_island7" class="gridblock grid_special_island7 <?php echo $gameIsland7; ?>" data-islandNum="7" title="This island is worth 7 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
                    <div id="special_island7_pop" class="special_island7 special_island3x3 <?php echo $gameIsland7; ?>" data-islandNum="7" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                        <div <?php echo $landPositionClass; ?> id="pos7a" data-positionId="97" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[97]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos7b" data-positionId="98" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[98]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos7c" data-positionId="99" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[99]; ?></div>
                        <div class="gridblockTiny missileContainer" id="posM3" data-positionId="123" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[123]; ?></div>
                    </div>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="27" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[27]; ?></div>
                <div class="gridblock grid_special_island5_2  <?php echo $gameIsland5; ?>" title="This island is worth 8 Reinforcement Points" data-islandNum="5" id="special_island5_extra" <?php echo $gridIslandFunctions; ?>>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="28" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[28]; ?></div>
                <div id="special_island8" class="gridblock grid_special_island8 <?php echo $gameIsland8; ?>" data-islandNum="8" title="This island is worth 10 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
                    <div id="special_island8_pop" class="special_island8 special_island3x3 <?php echo $gameIsland8; ?>" data-islandNum="8" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                        <div <?php echo $landPositionClass; ?> id="pos8a" data-positionId="100" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[100]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos8b" data-positionId="101" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[101]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos8c" data-positionId="102" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[102]; ?></div>
                    </div>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="29" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[29]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="30" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[30]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="31" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[31]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="32" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[32]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="33" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[33]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="34" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[34]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="35" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[35]; ?></div>
                <div id="special_island9" class="gridblock grid_special_island9 <?php echo $gameIsland9; ?>" data-islandNum="9" title="This island is worth 8 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
                    <div id="special_island9_pop" class="special_island9 special_island3x3 <?php echo $gameIsland9; ?>" data-islandNum="9" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                        <div <?php echo $landPositionClass; ?> id="pos9a" data-positionId="103" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[103]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos9b" data-positionId="104" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[104]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos9c" data-positionId="105" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[105]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos9d" data-positionId="106" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[106]; ?></div>
                        <div class="gridblockTiny missileContainer" id="posM4" data-positionId="124" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[124]; ?></div>
                    </div>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="36" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[36]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="37" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[37]; ?></div>
                <div id="special_island10" class="gridblock grid_special_island10 <?php echo $gameIsland10; ?>" data-islandNum="10" title="This island is worth 5 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
                    <div id="special_island10_pop" class="special_island10 special_island3x3 <?php echo $gameIsland10; ?>" data-islandNum="10" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                        <div <?php echo $landPositionClass; ?> id="pos10a" data-positionId="107" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[107]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos10b" data-positionId="108" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[108]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos10c" data-positionId="109" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[109]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos10d" data-positionId="110" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[110]; ?></div>
                    </div>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="38" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[38]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="39" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[39]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="40" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[40]; ?></div>
                <div id="special_island11" class="gridblock grid_special_island11 <?php echo $gameIsland11; ?>" data-islandNum="11" title="This island is worth 5 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
                    <div id="special_island11_pop" class="special_island11 special_island3x3 <?php echo $gameIsland11; ?>" data-islandNum="11" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                        <div <?php echo $landPositionClass; ?> id="pos11a" data-positionId="111" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[111]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos11b" data-positionId="112" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[112]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos11c" data-positionId="113" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[113]; ?></div>
                    </div>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="41" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[41]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="42" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[42]; ?></div>
                <div id="special_island12" class="gridblock grid_special_island12 <?php echo $gameIsland12; ?>" data-islandNum="12" title="This island is worth 5 Reinforcement Points" <?php echo $gridIslandFunctions; ?>>
                    <div id="special_island12_pop" class="special_island12 special_island3x3 <?php echo $gameIsland12; ?>" data-islandNum="12" data-placementId="-1" <?php echo $popIslandFunctions; ?>>
                        <div <?php echo $landPositionClass; ?> id="pos12a" data-positionId="114" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[114]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos12b" data-positionId="115" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[115]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos12c" data-positionId="116" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[116]; ?></div>
                        <div <?php echo $landPositionClass; ?> id="pos12d" data-positionId="117" <?php echo $landFunctions; ?>><?php echo $finalPieceHTML[117]; ?></div>
                    </div>
                </div>
                <div <?php echo $waterClass; ?> data-positionId="43" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[43]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="44" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[44]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="45" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[45]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="46" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[46]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="47" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[47]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="48" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[48]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="49" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[49]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="50" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[50]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="51" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[51]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="52" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[52]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="53" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[53]; ?></div>
                <div <?php echo $waterClass; ?> data-positionId="54" <?php echo $waterFunctions; ?>><?php echo $finalPieceHTML[54]; ?></div>
                <div id="battleZonePopup">
                    <div id="unused_attacker"><?php $boxId = 1; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
                    <div id="unused_defender"><?php $boxId = 2; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
                    <div id="used_attacker"><?php $boxId = 5; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
                    <div id="used_defender"><?php $boxId = 6; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
                    <div id="center_attacker"><?php $boxId = 3; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
                    <div id="center_defender"><?php $boxId = 4; include("backend/game/battles/battlePieceDisplay.php"); ?></div>
                    <div id="battle_outcome"></div>
                    <div id="battle_buttons">
                        <button id="attackButton" disabled onclick="attackButton.disabled = true; generalBackendRequest('backend/game/battles/battleAttackButton.php');">Loading...</button>
                        <button id="changeSectionButton" disabled onclick="generalBackendRequest('backend/game/battles/battleChangeSectionButton.php')">Loading...</button>
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
                            <button id="actionPopupButton" disabled onclick="actionPopupButton.disabled = true; generalBackendRequest('backend/game/battles/battleActionPopupButton.php');">Loading...</button>
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
                    </div>
                </div>
            </div>
        </div>
        <script src="frontend/js/game-v5.js"></script>
    </body>
</html>
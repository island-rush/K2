<?php
session_start();
include("../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$query = 'SELECT gamePhase, gameCurrentTeam, gameRedRpoints, gameBlueRpoints, gameRedHpoints, gameBlueHpoints, gameBattleSection, gameBattleSubSection, gameBattleLastRoll, gameBattleLastMessage, gameBattlePosSelected FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();

$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$gameRedRpoints = $r['gameRedRpoints'];
$gameBlueRpoints = $r['gameBlueRpoints'];
$gameRedHpoints = $r['gameRedHpoints'];
$gameBlueHpoints = $r['gameBlueHpoints'];
$gameBattleSection = $r['gameBattleSection'];
$gameBattleSubSection = $r['gameBattleSubSection'];
$gameBattleLastRoll = $r['gameBattleLastRoll'];
$gameBattlePosSelected = $r['gameBattlePosSelected'];
$gameBattleLastMessage = $r['gameBattleLastMessage'];

$query2 = "SELECT newsText, newsEffectText FROM newsAlerts WHERE newsGameId = ? AND newsActivated = 1 AND newsLength != 0 ORDER BY newsOrder DESC";
$preparedQuery2 = $db->prepare($query2);
$preparedQuery2->bind_param("i", $gameId);
$preparedQuery2->execute();
$results2 = $preparedQuery2->get_result();
$r2 = $results2->fetch_assoc();

$newsText = $r2['newsText'];
$newsEffectText = $r2['newsEffectText'];

if ($gameBattleSection == "attack") {  //always going to be choosing pieces, otherwise wouldn't hit this button
    $order = "ASC";  // 3 attacking 4
} else {
    $order = "DESC"; // 4 attacking 3
}

$query3 = 'SELECT placementUnitId FROM battlePieces RIGHT JOIN placements ON battlePieceId WHERE battlePieceId = placementId AND battleGameId = ? AND (battlePieceState = 3 or battlePieceState = 4) ORDER BY battlePieceState '.$order;
$preparedQuery3 = $db->prepare($query3);
$preparedQuery3->bind_param("i", $gameId);
$preparedQuery3->execute();
$results3 = $preparedQuery3->get_result();
$numResults3 = $results3->num_rows;

//undo button disabled
if (($myTeam != "Spec") && (($gamePhase == 2 || $gamePhase == 3 || $gamePhase == 4) && ($myTeam == $gameCurrentTeam)) && $gameBattleSection == "none") {
    $undo_disabled = false;
} else {
    $undo_disabled = true;
}

//control (battle) button disabled
if (($myTeam != $gameCurrentTeam) || $gameBattleSection == "attack" || $gameBattleSection == "counter" || $gameBattleSection == "askRepeat" || ($gamePhase != 5 && $gamePhase != 2)) {
    $control_button_disabled = true;
} else {
    $control_button_disabled = false;
}

//control button text / fucnction
if ($gameBattleSection == "none" && $gamePhase == 2) {
    $control_button_text = "Start Battle";
} elseif ($gameBattleSection == "selectPos") {
    $control_button_text = "Done Selecting Pos";
} elseif ($gameBattleSection == "selectPieces") {
    $control_button_text = "Done Selecting Pieces";
} elseif ($gamePhase == 5) {
    $control_button_text = "Hybrid Tool";
} else {
    $control_button_text = "No Function";
}

//next phase disabled
if ($gameBattleSection == "none" && ($myTeam == $gameCurrentTeam)) {
    $next_phase_disabled = false;
} else {
    $next_phase_disabled = true;  //battle is going on
}

//news popped
if ($gamePhase == 0) {
    $news_popped = true;
} else {
    $news_popped = false;
}

if ($gamePhase == 0) {
    $newsTitle = "News Alert";
} else {
    $newsTitle = "Hybrid Warfare Menu";
}

//battle popped
if ($gameBattleSection == "attack" || $gameBattleSection == "counter" || $gameBattleSection == "askRepeat") {
    $battle_popped = true;
} else {
    $battle_popped = false;
}

//battle action popup up or down
if ($gameBattleSubSection == "choosing_pieces") {
    $battle_action_popped = false;
} else {
    $battle_action_popped = true;  //for defense_bonus, after_action
}

//attack button disabled (based on team and center pieces (client))
//change section button disabled (based on team and section) (same as attack, does not depend on client pieces)
$battleOutcome = "";
if (($myTeam != "Spec") && ((($gameBattleSection == "attack" || $gameBattleSection == "askRepeat") && $myTeam == $gameCurrentTeam) || ($gameBattleSection == "counter" && $myTeam != $gameCurrentTeam))) {
    if ($numResults3 == 2) {  //both piece in the center
        $attack_button_disabled = false;
        $r5 = $results3->fetch_assoc();
        $attackUnitId = $r5['placementUnitId'];
        $r5 = $results3->fetch_assoc();
        $defendUnitId = $r5['placementUnitId'];
        $valueNeeded = $_SESSION['attack'][$attackUnitId][$defendUnitId];
        $battleOutcome = "You must roll a ".$valueNeeded." or higher in order to hit.";
    } else {
        if ($gameBattleSection == "askRepeat") {
            $attack_button_disabled = false;
        } else {
            $attack_button_disabled = true;
        }
    }
    $change_section_button_disabled = false;
} else {
    $attack_button_disabled = true;
    $change_section_button_disabled = true;
}

//attack button text (based on battle section and subsection)
if ($gameBattleSection == "attack") {
    $attack_button_text = "Attack";
} elseif ($gameBattleSection == "counter") {
    $attack_button_text = "Counter Attack";
} else {
    $attack_button_text = "Repeat Battle";
}

//change section button text
if ($gameBattleSection == "attack") {
    $change_section_button_text = "End Attack";
} elseif ($gameBattleSection == "counter") {
    $change_section_button_text = "End Counter";
} else {
    $change_section_button_text = "End Battle";
}

//battle adjacent pieces for selection
$battleAdjacentPlacementIds = [];
if ($gameBattleSection == "selectPieces" && $myTeam == $gameCurrentTeam) {  //only give adjacent array to selecting team
    $adjacentPositions = [];
    array_push($adjacentPositions, $gameBattlePosSelected);
    $n = sizeof($_SESSION['dist'][0]);
    for ($j = 0; $j < $n; $j++) {
        if ($_SESSION['dist'][$gameBattlePosSelected][$j] == 1) {
            array_push($adjacentPositions, $j);
        }
    }
    for ($x = 0; $x < sizeof($adjacentPositions); $x++) {
        $thisPositionId = $adjacentPositions[$x];
        $query = 'SELECT placementId FROM placements WHERE placementBattleUsed = 0 AND placementPositionId = ? AND placementTeamId = ? AND placementGameId = ? AND placementContainerId = -1 AND placementUnitId < 11';
        if ($thisPositionId == $gameBattlePosSelected) {
            $query = 'SELECT placementId FROM placements WHERE placementBattleUsed = 0 AND placementPositionId = ? AND placementTeamId = ? AND placementGameId = ? AND placementContainerId = -1 AND placementUnitId < 15';
        }
        $query = $db->prepare($query);
        $query->bind_param("isi", $thisPositionId, $myTeam, $gameId);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        for ($i = 0; $i < $num_results; $i++) {
            $r = $results->fetch_assoc();
            $thisPlacementId = $r['placementId'];
            array_push($battleAdjacentPlacementIds, $thisPlacementId);
        }
    }
}

if ($gameBattleSubSection == "continue_choosing") {
    $actionPopupButtonText = "Click to go back to choosing.";
    if (($gameBattleSection == "attack" && $myTeam == $gameCurrentTeam) || ($gameBattleSection == "counter" && $myTeam != $gameCurrentTeam)) {
        $actionPopupButtonDisabled = false;
    } else {
        $actionPopupButtonDisabled = true;
    }
} else {
    $actionPopupButtonText = "Click to roll Defense Bonus.";
    if (($gameBattleSection == "attack" && $myTeam == $gameCurrentTeam)) {
        $actionPopupButtonDisabled = true;  //wouldn't get defense bonus from counter, only in attack section
    } else {
        $actionPopupButtonDisabled = false;
    }
}

$arr = array(
    'gamePhase' => (int) $gamePhase,
    'gameRedRpoints' => (int) $gameRedRpoints,
    'gameBlueRpoints' => (int) $gameBlueRpoints,
    'gameRedHpoints' => (int) $gameRedHpoints,
    'gameBlueHpoints' => (int) $gameBlueHpoints,
    'undo_disabled' => $undo_disabled,
    'control_button_disabled' => $control_button_disabled,
    'control_button_text' => $control_button_text,
    'next_phase_disabled' => $next_phase_disabled,
    'newsTitle' => $newsTitle,
    'newsText' => $newsText,
    'newsEffectText' => $newsEffectText,
    'news_popped' => $news_popped,
    'battle_popped' => $battle_popped,
    'battle_action_popped' => $battle_action_popped,
    'gameBattleLastRoll' => $gameBattleLastRoll,
    'gameBattleLastMessage' => $gameBattleLastMessage,
    'actionPopupButtonDisabled' => $actionPopupButtonDisabled,
    'actionPopupButtonText' => $actionPopupButtonText,
    'attack_button_disabled' => $attack_button_disabled,
    'attack_button_text' => $attack_button_text,
    'change_section_button_disabled' => $change_section_button_disabled,
    'change_section_button_text' => $change_section_button_text,
    'gameBattleSection' => $gameBattleSection,
    'gameBattlePosSelected' => $gameBattlePosSelected,
    'battleAdjacentPlacementIds' => $battleAdjacentPlacementIds,
    'gameCurrentTeam' => $gameCurrentTeam,
    'battleOutcome' => $battleOutcome
);

echo json_encode($arr);
$db->close();

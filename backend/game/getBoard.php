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
$gameBattleLastMessage = $r['gameBattleLastMessage'];
$gameBattlePosSelected = $r['gameBattlePosSelected'];


$activated = 1;
$zero = 0;
$query2 = "SELECT newsText, newsEffectText FROM newsAlerts WHERE newsGameId = ? AND newsActivated = ? AND newsLength != ? ORDER BY newsOrder DESC";
$preparedQuery2 = $db->prepare($query2);
$preparedQuery2->bind_param("iii", $gameId, $activated, $zero);
$preparedQuery2->execute();
$results2 = $preparedQuery2->get_result();
$r2 = $results2->fetch_assoc();

$newsText = $r2['newsText'];
$newsEffectText = $r2['newsEffectText'];

$location5 = 5;
$location6 = 6;
$query3 = "SELECT battlePieceId FROM battlePieces WHERE battleGameId = ? AND (battlePieceState = ? OR battlePieceState = ?)";
$preparedQuery3 = $db->prepare($query3);
$preparedQuery3->bind_param("iii", $gameId, $location5, $location6);
$preparedQuery3->execute();
$results3 = $preparedQuery3->get_result();
$numResults3 = $results3->num_rows;

//undo button disabled
if (($myTeam != "Spec") && (($gamePhase == 2 || $gamePhase == 3 || $gamePhase == 4) && ($myTeam == $gameCurrentTeam))) {
    $undo_disabled = false;
} else {
    $undo_disabled = true;
}

//control (battle) button disabled
if (($myTeam == "Spec") || $gameBattleSection == "attack" || $gameBattleSection == "counter" || $gameBattleSection == "askRepeat" || $gamePhase != 5) {
    $control_button_disabled = true;
} else {
    $control_button_disabled = false;
}

//control button text
if ($gameBattleSection == "none" && $gamePhase == 2) {
    $control_button_text = "Select Pos";
} elseif ($gameBattleSection == "selectPos") {
    $control_button_text = "Select Pieces";
} elseif ($gameBattleSection == "selectPieces") {
    $control_button_text = "Start Battle";
} elseif ($gamePhase == 5) {
    $control_button_text = "Hybrid Tool";
} else {
    $control_button_text = "No Function";
}

//next phase disabled
if ($gameBattleSection == "none" && ($myTeam != "Spec")) {
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
if (($myTeam != "Spec") && ((($gameBattleSection == "attack" || $gameBattleSection == "askRepeat") && $myTeam == $gameCurrentTeam) || ($gameBattleSection == "counter" && $myTeam != $gameCurrentTeam))) {
    if ($numResults3 == 2) {  //both piece in the center
        $attack_button_disabled = false;
    } else {
        $attack_button_disabled = true;
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

//what should the user feedback say
$user_feedback = "Click to advance the phase when complete.";


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
    'attack_button_disabled' => $attack_button_disabled,
    'attack_button_text' => $attack_button_text,
    'change_section_button_disabled' => $change_section_button_disabled,
    'change_section_button_text' => $change_section_button_text,
    'user_feedback' => (string) $user_feedback
);

echo json_encode($arr);

$db->close();

// Javascript File for Island Rush Game (Main game page)
console.log("Island Rush Game Javascript");

//global variables and DOM caching
let phaseNames = ['News', 'Buy Reinforcements', 'Combat', 'Fortify Move', 'Reinforcement Place', 'Hybrid War', 'Round Recap'];
let unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
let purchased_container = document.getElementById("purchased_container");
let phase_indicator = document.getElementById("phase_indicator");
let red_rPoints_indicator = document.getElementById("red_rPoints_indicator");
let blue_rPoints_indicator = document.getElementById("blue_rPoints_indicator");
let red_hPoints_indicator = document.getElementById("red_hPoints_indicator");
let blue_hPoints_indicator = document.getElementById("blue_hPoints_indicator");
let undo_button = document.getElementById("undo_button");
let control_button = document.getElementById("control_button");
let user_feedback = document.getElementById("user_feedback");
let phase_button = document.getElementById("phase_button");
let battleZonePopup = document.getElementById("battleZonePopup");
let unused_attacker = document.getElementById("unused_attacker");
let unused_defender = document.getElementById("unused_defender");
let used_attacker = document.getElementById("used_attacker");
let used_defender = document.getElementById("used_defender");
let center_attacker = document.getElementById("center_attacker");
let center_defender = document.getElementById("center_defender");
let attackButton = document.getElementById("attackButton");
let changeSectionButton = document.getElementById("changeSectionButton");
let battleActionPopup = document.getElementById("battleActionPopup");
let lastBattleMessage = document.getElementById("lastBattleMessage");
let actionPopupButton = document.getElementById("actionPopupButton");
let dice_image1 = document.getElementById("dice_image1");
let dice_image2 = document.getElementById("dice_image2");
let dice_image3 = document.getElementById("dice_image3");
let dice_image4 = document.getElementById("dice_image4");
let dice_image5 = document.getElementById("dice_image5");
let dice_image6 = document.getElementById("dice_image6");
let dice = [dice_image1, dice_image2, dice_image3, dice_image4, dice_image5, dice_image6];

let smallDelay = 50;

let popupTitle = document.getElementById("popupTitle");
let newsBodyText = document.getElementById("newsBodyText");
let newsBodySubText = document.getElementById("newsBodySubText");
let popupBodyNews = document.getElementById("popupBodyNews");
let popupBodyHybridMenu = document.getElementById("popupBodyHybridMenu");
let popup = document.getElementById("popup");

//-------------------------------------------------------------------------------------------------------------------





function getPanel(){
	let phpPhaseChange = new XMLHttpRequest();
	phpPhaseChange.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			let decoded = JSON.parse(this.responseText);
			phase_indicator.innerHTML = "Current Phase: " + phaseNames[decoded.gamePhase];
			red_rPoints_indicator.innerHTML = decoded.gameRedRpoints;
			blue_rPoints_indicator.innerHTML = decoded.gameBlueRpoints;
			red_hPoints_indicator.innerHTML = decoded.gameRedHpoints;
			blue_hPoints_indicator.innerHTML = decoded.gameBlueHpoints;
			undo_button.disabled = decoded.undo_disabled;
			control_button.disabled = decoded.control_button_disabled;
			control_button.innerHTML = decoded.control_button_text;
			phase_button.disabled = decoded.next_phase_disabled;
			popupTitle.innerHTML = decoded.newsTitle;
			newsBodyText.innerHTML = decoded.newsText;
			newsBodySubText.innerHTML = decoded.newsEffectText;
			if (decoded.news_popped) {
				popup.style.display = "block";
				popupBodyNews.style.display = "block";
			} else {
				popup.style.display = "none";
				popupBodyNews.style.display = "none";
			}
			if (decoded.battle_popped) {
				battleZonePopup.style.display = "block";
			} else {
				battleZonePopup.style.display = "none";
			}
			if (decoded.battle_action_popped) {
				battleActionPopup.style.display = "block";
			} else {
				battleActionPopup.style.display = "none";
			}
			showDice(decoded.gameBattleLastRoll);
			attackButton.disabled = decoded.attack_button_disabled;
			attackButton.innerHTML = decoded.attack_button_text;
			changeSectionButton.disabled = decoded.change_section_button_disabled;
			changeSectionButton.innerHTML = decoded.change_section_button_text;
			user_feedback.innerHTML = decoded.user_feedback;
		}
	};
	phpPhaseChange.open("GET", "backend/game/getBoard.php", true);
	phpPhaseChange.send();
}
getPanel();



function ajaxUpdate(){
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText === "TIMEOUT") {
				window.setTimeout("ajaxUpdate();", smallDelay);
			} else {
				let decoded = JSON.parse(this.responseText);

				lastUpdateId = decoded.updateId;

				let updateType = decoded.updateType;

				if (updateType === "phaseChange") {
					getPanel();
				}

				if (updateType === "piecePurchase") {
					purchased_container.innerHTML += decoded.updateBattlePiecesSelected;
					getPanel();
				}

				window.setTimeout("ajaxUpdate();", smallDelay);
			}
		}
	};
	phpUpdateBoard.open("GET", "backend/game/ajaxUpdate.php?gameId=" + gameId + "&lastUpdateId=" + lastUpdateId, true);
	phpUpdateBoard.send();
}
ajaxUpdate();




//button functions---------------------------------------------------
function controlButtonFunction() {
	//hit the server for control button click
}

function nextPhaseButtonFunction(){
	if (confirm("Are you sure you want to complete this phase?")) {
		let phpUpdateBoard = new XMLHttpRequest();
		phpUpdateBoard.open("GET", "backend/game/phaseChange.php", true);
		phpUpdateBoard.send();
	}
}

function attackButtonFunction(){
	//hit the server for attack button click
}

function changeSectionButtonFunction(){
	//hit the server for change section button click
}

function undoButtonFunction(){
	//hit the server for undo button click
}

function hybridAirfieldShutdownButtonFunction(){

}
function hybridBankDrainButtonFunction(){

}
function hybridAddMoveButtonFunction(){

}
function hybridDeletePieceButtonFunction(){

}
function hybridAircraftDisableButtonFunction(){

}
function hybridNukeIslandButtonFunction(){

}
function hybridHumanitarianButtonFunction(){

}

function purchasePieceFunction(unitId){
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.open("GET", "backend/game/pieces/piecePurchase.php?unitId=" + unitId, true);
	phpUpdateBoard.send();
}
//----------------------------------------------------------------


































function showDice(diceNumber){
	dice[0].style.display = "none";
	dice[1].style.display = "none";
	dice[2].style.display = "none";
	dice[3].style.display = "none";
	dice[4].style.display = "none";
	dice[5].style.display = "none";
	dice[diceNumber-1].style.display = "block";
}




function logout(){
	// let phpUpdateBoard = new XMLHttpRequest();
	// phpUpdateBoard.open("GET", "backend/game/logout.php", true);
	// phpUpdateBoard.send();
	window.location.replace("backend/game/logout.php");
}

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


let popIsland1 = document.getElementById("special_island1_pop");
let popIsland2 = document.getElementById("special_island2_pop");
let popIsland3 = document.getElementById("special_island3_pop");
let popIsland4 = document.getElementById("special_island4_pop");
let popIsland5 = document.getElementById("special_island5_pop");
let popIsland6 = document.getElementById("special_island6_pop");
let popIsland7 = document.getElementById("special_island7_pop");
let popIsland8 = document.getElementById("special_island8_pop");
let popIsland9 = document.getElementById("special_island9_pop");
let popIsland10 = document.getElementById("special_island10_pop");
let popIsland11 = document.getElementById("special_island11_pop");
let popIsland12 = document.getElementById("special_island12_pop");
let popIslands = [popIsland1, popIsland2, popIsland3, popIsland4, popIsland5, popIsland6, popIsland7, popIsland8, popIsland9, popIsland10, popIsland11, popIsland12];

let gridIsland1 = document.getElementById("special_island1");
let gridIsland2 = document.getElementById("special_island2");
let gridIsland3 = document.getElementById("special_island3");
let gridIsland4 = document.getElementById("special_island4");
let gridIsland5 = document.getElementById("special_island5");
let gridIsland6 = document.getElementById("special_island6");
let gridIsland7 = document.getElementById("special_island7");
let gridIsland8 = document.getElementById("special_island8");
let gridIsland9 = document.getElementById("special_island9");
let gridIsland10 = document.getElementById("special_island10");
let gridIsland11 = document.getElementById("special_island11");
let gridIsland12 = document.getElementById("special_island12");
let gridIslands = [gridIsland1, gridIsland2, gridIsland3, gridIsland4, gridIsland5, gridIsland6, gridIsland7, gridIsland8, gridIsland9, gridIsland10, gridIsland11, gridIsland12];

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

				if (updateType === "logout") {
					alert("Teacher has disabled the game...logging out.");  //TODO: don't log out spectators
					logout();
				}

				if (updateType === "phaseChange") {
					getPanel();
				}

				if (updateType === "piecePurchase") {
					purchased_container.innerHTML += decoded.updateBattlePiecesSelected;
					getPanel();
				}

				if (updateType === "userFeedback") {
					user_feedback.innerHTML = decoded.updateBattlePiecesSelected;
				}

				if (updateType === "pieceMove") {
					ajaxPieceMove(parseInt(decoded.updatePlacementId), parseInt(decoded.updateNewPositionId), parseInt(decoded.updateNewContainerId));
				}

				if (updateType === "pieceKilled") {
					ajaxPieceKilled(parseInt(decoded.updatePlacementId));
				}

				window.setTimeout("ajaxUpdate();", smallDelay);
			}
		}
	};
	phpUpdateBoard.open("GET", "backend/game/ajaxUpdate.php?gameId=" + gameId + "&lastUpdateId=" + lastUpdateId, true);
	phpUpdateBoard.send();
}
ajaxUpdate();

function ajaxPieceMove(placementId, toPositionId, toContainerId) {
	let gamePiece = document.querySelector("[data-placementId='" + placementId + "']");
	let newLocation;
	if (toContainerId === -1) {
		newLocation = document.querySelector("[data-positionId='" + toPositionId + "']");
	} else {
		newLocation = document.querySelector("[data-placementId='" + toContainerId + "']").firstChild;
	}
	newLocation.append(gamePiece);
}

function ajaxPieceKilled(placementId) {
	//TODO: make all document selectors check for null returns before .removing (see updatePieceDelete() in K2)
	document.querySelector("[data-placementId='" + placementId + "']").remove();
}


//button functions---------------------------------------------------
function controlButtonFunction() {
	event.preventDefault();

	event.stopPropagation();
}

function nextPhaseButtonFunction(){
	event.preventDefault();
	if (confirm("Are you sure you want to complete this phase?")) {
		let phpUpdateBoard = new XMLHttpRequest();
		phpUpdateBoard.open("GET", "backend/game/phaseChange.php", true);
		phpUpdateBoard.send();
	}
	event.stopPropagation();
}

function attackButtonFunction(){
	event.preventDefault();

	event.stopPropagation();
}

function changeSectionButtonFunction(){
	event.preventDefault();

	event.stopPropagation();
}

function undoButtonFunction(){
	event.preventDefault();

	event.stopPropagation();
}

function hybridAirfieldShutdownButtonFunction(){
	event.preventDefault();

	event.stopPropagation();
}
function hybridBankDrainButtonFunction(){
	event.preventDefault();

	event.stopPropagation();
}
function hybridAddMoveButtonFunction(){
	event.preventDefault();

	event.stopPropagation();
}
function hybridDeletePieceButtonFunction(){
	event.preventDefault();

	event.stopPropagation();
}
function hybridAircraftDisableButtonFunction(){
	event.preventDefault();

	event.stopPropagation();
}
function hybridNukeIslandButtonFunction(){
	event.preventDefault();

	event.stopPropagation();
}
function hybridHumanitarianButtonFunction(){
	event.preventDefault();

	event.stopPropagation();
}

function purchasePieceFunction(unitId){
	event.preventDefault();
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.open("GET", "backend/game/pieces/piecePurchase.php?unitId=" + unitId, true);
	phpUpdateBoard.send();
	event.stopPropagation();
}
//----------------------------------------------------------------





function gridIslandClick(callingElement){
	event.preventDefault();

	//close all open islands
	unpopIslands();

	let islandNum = callingElement.getAttribute("data-islandNum");

	//pop this island
	popIslands[islandNum-1].style.display = "block";
	gridIslands[islandNum-1].style.zIndex = 20;  //default for a gridblock is 10

	event.stopPropagation();
}

function unpopIslands() {
	for (let x = 0; x < popIslands.length; x++) {
		popIslands[x].style.display = "none";
		popIslands[x].parentNode.style.zIndex = 10;  //10 is the default
	}
}


function waterClick(){
	event.preventDefault();

	unpopIslands();

	event.stopPropagation();
}






function pieceDragstart(event, callingElement){
	//only need the placementId to know what is being dragged, server side will handle everything else
	event.dataTransfer.setData("placementId", callingElement.getAttribute("data-placementId"));
	event.stopPropagation();
}


function positionDragover(event, callingElement){
	event.preventDefault();
	//TODO: client side checks to prevent dragging pieces
	//Can't Drop into something draggable (other pieces) (containers are non-draggable)
	if (callingElement.getAttribute("draggable") === "true") {
		event.dataTransfer.dropEffect = "none";
	} else {
		event.dataTransfer.dropEffect = "all";
	}
}


function positionDrop(event, callingElement){
	event.preventDefault();

	let placementId = event.dataTransfer.getData("placementId");  //what piece was dropped
	let positionId = parseInt(callingElement.getAttribute("data-positionId"));  //-1 if going into container
	let containerId = parseInt(callingElement.parentNode.getAttribute("data-placementId"));  //-1 if going into position

	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			user_feedback.innerHTML = this.responseText;
		}
	};
	phpUpdateBoard.open("GET", "backend/game/pieces/pieceMove.php?placementId=" + placementId + "&positionId=" + positionId + "&containerId=" + containerId, true);
	phpUpdateBoard.send();

	event.stopPropagation();
}









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
	event.preventDefault();
	window.location.replace("backend/game/logout.php");
	event.stopPropagation();
}

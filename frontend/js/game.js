// Javascript File for Island Rush Game (Main game page)
console.log("Island Rush Game Javascript");

//global variables and DOM caching
let phaseNames = ['News', 'Buy Reinforcements', 'Combat', 'Fortify Move', 'Reinforcement Place', 'Hybrid War', 'Round Recap'];
let unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
let whole_game = document.getElementById("whole_game");
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

let openContainerPiece = null;
let openPopupIslandNum = 0;

let pieceDragTimer;
let hybridTimer;
let islandTimer;

let hybridDeletePieceState = false;
let hybridDisableAirfieldState = false;
let hybridBankState = false;
let hybridNukeState = false;
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
			// user_feedback.innerHTML = decoded.user_feedback;  //TODO: use phase change to update points, overrides other user feedback
		}
	};
	phpPhaseChange.open("GET", "backend/game/getBoard.php", true);
	phpPhaseChange.send();
}
getPanel();

// Ajax functions --------------------------------------------------------
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

				if (updateType === "islandOwnerChange") {
					ajaxIslandOwnerChange(parseInt(decoded.updateIsland), decoded.updateIslandTeam);
				}

				if (updateType === "piecePurchase") {
					purchased_container.innerHTML += decoded.updateBattlePiecesSelected;
					getPanel();
				}

				if (updateType === "pieceMove") {
					ajaxPieceMove(parseInt(decoded.updatePlacementId), parseInt(decoded.updateNewPositionId), parseInt(decoded.updateNewContainerId));
				}

				if (updateType === "pieceRemove") {
					ajaxPieceRemove(parseInt(decoded.updatePlacementId));
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

function ajaxIslandOwnerChange(islandNum, newTeamId) {
	let gridIsland = gridIslands[islandNum-1];
	let popIsland = popIslands[islandNum-1];
	let oldTeamId = "Red";
	if (newTeamId === "Red") {
		oldTeamId = "Blue";
	}
	gridIsland.classList.remove(oldTeamId);
	gridIsland.classList.add(newTeamId);
	popIsland.classList.remove(oldTeamId);
	popIsland.classList.add(newTeamId);
}

function ajaxPieceRemove(placementId) {
	//TODO: make all document selectors check for null returns before .removing (see updatePieceDelete() in K2)
	document.querySelector("[data-placementId='" + placementId + "']").remove();
}
//----------------------------------------------------------------

function unpopIslands() {
	if (openPopupIslandNum !== 0) {
		popIslands[openPopupIslandNum-1].style.display = "none";
		gridIslands[openPopupIslandNum-1].style.zIndex = 10;
		openPopupIslandNum = 0;
	}
}

function closeContainer() {
	if (openContainerPiece != null) {
		openContainerPiece.firstChild.style.display = "none";
		openContainerPiece.style.zIndex = 15;
		openContainerPiece.parentNode.style.zIndex = 10;
		openContainerPiece = null;
	}
}

function gridIslandClick(event, callingElement){
	event.preventDefault();

	if (hybridBankState) {
		if (callingElement.classList[2] !== myTeam) {
			callingElement.classList.add("selectedPos");
			hybridTimer = setTimeout(function() {
				if (confirm("Are you sure you want this island's points for next two turns?")) {
					let islandNum = parseInt(callingElement.getAttribute("data-islandNum"));
					let phpUpdateBoard = new XMLHttpRequest();
					phpUpdateBoard.onreadystatechange = function () {
						if (this.readyState === 4 && this.status === 200) {
							user_feedback.innerHTML = this.responseText;
						}
					};
					phpUpdateBoard.open("POST", "hybridBankDrain.php?islandNum=" + islandNum, true);
					phpUpdateBoard.send();

					whole_game.style.backgroundColor = "black";
					hybridBankState = false;
					control_button.disabled = false;
					phase_button.disabled = false;
					callingElement.classList.remove("selectedPos");  //could put this after the confirm if statement by itself
				} else {
					callingElement.classList.remove("selectedPos");
				}
			}, 50);
		} else {
			user_feedback.innerHTML = "Can't select Bank Option for your own island.";
		}
	} else if (hybridNukeState) {
		callingElement.classList.add("selectedPos");
		hybridTimer = setTimeout(function() {
			if (confirm("Are you sure you want to nuke this island?")) {
				let islandNum = parseInt(callingElement.getAttribute("data-islandNum"));

				let phpUpdateBoard = new XMLHttpRequest();
				phpUpdateBoard.onreadystatechange = function () {
					if (this.readyState === 4 && this.status === 200) {
						user_feedback.innerHTML = this.responseText;
					}
				};
				phpUpdateBoard.open("POST", "hybridNuke.php?islandNum=" + islandNum, true);
				phpUpdateBoard.send();

				whole_game.style.backgroundColor = "black";
				hybridNukeState = false;
				control_button.disabled = false;
				phase_button.disabled = false;
				callingElement.classList.remove("selectedPos");
			} else {
				callingElement.classList.remove("selectedPos");
			}
		}, 50);
	} else {
		unpopIslands();  //close all open islands
		closeContainer();
		let islandNum = callingElement.getAttribute("data-islandNum");
		popIslands[islandNum-1].style.display = "block";
		gridIslands[islandNum-1].style.zIndex = 20;  //default for a gridblock is 10
		openPopupIslandNum = islandNum;
	}

	event.stopPropagation();
}

function waterClick(){
	unpopIslands();
	closeContainer();
}

function landClick(event, callingElement) {
	event.preventDefault();

	if (hybridDisableAirfieldState) {
		callingElement.classList.add("selectedPos");
		let positionId = parseInt(callingElement.getAttribute("data-positionId"));
		let listairfields = [56, 57, 78, 83, 89, 113, 116, 66, 68];
		if (listairfields.includes(positionId)) {
			hybridTimer = setTimeout(function() {
				if (confirm("Is this the airfield you want to disable?")) {
					let phpUpdateBoard = new XMLHttpRequest();
					phpUpdateBoard.onreadystatechange = function () {
						if (this.readyState === 4 && this.status === 200) {
							user_feedback.innerHTML = this.responseText;
						}
					};
					phpUpdateBoard.open("POST", "hybridDisableAirfield.php?positionId=" + positionId, true);
					phpUpdateBoard.send();

					callingElement.classList.remove("selectedPos");
					whole_game.style.backgroundColor = "black";
					hybridDisableAirfieldState = false;
					control_button.disabled = false;
					phase_button.disabled = false;
				} else {
					callingElement.classList.remove("selectedPos");
				}
			}, 50);
		} else {
			callingElement.classList.remove("selectedPos");
			user_feedback.innerHTML = "Not a valid Airfield position.";
		}
	}

	event.stopPropagation();
}

//TODO: actually figure out timing mechanics for dragging over and popping
function containerDragenter(event, callingElement) {
	event.preventDefault();
	clearTimeout(pieceDragTimer);
	event.stopPropagation();
}

function containerDragleave(event, callingElement) {
	event.preventDefault();
	clearTimeout(pieceDragTimer);
	pieceDragTimer = setTimeout(function() { waterClick();}, 1000);
	event.stopPropagation();
}

function islandDragenter(event, callingElement) {
	event.preventDefault();
	clearTimeout(islandTimer);
	islandTimer = setTimeout(function() { gridIslandClick(event, callingElement);}, 1000);
	event.stopPropagation();
}

function islandDragleave(event, callingElement) {
	event.preventDefault();
	clearTimeout(islandTimer);
	event.stopPropagation();
}

function popupDragleave(event, callingElement) {
	event.preventDefault();
	clearTimeout(islandTimer);
	islandTimer = setTimeout(function() { unpopIslands();}, 1000);
	event.stopPropagation();
}

function popupDragover(event, callingElement) {
	event.preventDefault();
	clearTimeout(islandTimer);
	event.stopPropagation();
}

function popupDragenter(event, callingElement) {
	event.preventDefault();
	clearTimeout(islandTimer);
	// callingElement.classList.add("mouseOver");
	event.stopPropagation();
}

function pieceDragenter(event, callingElement) {
	event.preventDefault();
	clearTimeout(pieceDragTimer);
	let positionId = parseInt(callingElement.parentNode.getAttribute("data-positionId"));
	let unitName = callingElement.classList[0];
	if (unitName === "Transport" || unitName === "AircraftCarrier") {
		if (positionId !== 118) {
			clearTimeout(pieceDragTimer);
			pieceDragTimer = setTimeout(function() { pieceClick(event, callingElement);}, 1000);
		}
	}
	event.stopPropagation();
}

function pieceDragleave(event, callingElement){
	event.preventDefault();

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

function pieceTrash(event, callingElement) {
	event.preventDefault();
	let placementId = event.dataTransfer.getData("placementId");  //what piece was dropped
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			user_feedback.innerHTML = this.responseText;
		}
	};
	phpUpdateBoard.open("GET", "backend/game/pieces/pieceTrash.php?placementId=" + placementId, true);
	phpUpdateBoard.send();
	event.stopPropagation();
}

function piecePurchase(unitId){
	event.preventDefault();
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			user_feedback.innerHTML = this.responseText;
		}
	};
	phpUpdateBoard.open("GET", "backend/game/pieces/piecePurchase.php?unitId=" + unitId, true);
	phpUpdateBoard.send();
	event.stopPropagation();
}

function pieceClick(event, callingElement) {
	event.preventDefault();

	if (hybridDeletePieceState) {
		callingElement.classList.add("selected");
		hybridTimer = setTimeout(function() {
			if (confirm("Is this the piece you want to delete?")) {
				callingElement.classList.remove("selected");
				let placementId = parseInt(callingElement.getAttribute("data-placementId"));
				let phpUpdateBoard = new XMLHttpRequest();
				phpUpdateBoard.onreadystatechange = function () {
					if (this.readyState === 4 && this.status === 200) {
						user_feedback.innerHTML = this.responseText;
					}
				};
				phpUpdateBoard.open("POST", "hybridDeletePiece.php?placementId=" + placementId, true);
				phpUpdateBoard.send();

				whole_game.style.backgroundColor = "black";
				hybridDeletePieceState = false;
				control_button.disabled = false;
				phase_button.disabled = false;
			} else {
				callingElement.classList.remove("selected");
			}
		}, 50);
	} else {
		unpopIslands();
		closeContainer();

		let unitName = callingElement.classList[0];
		let positionId = parseInt(callingElement.parentNode.getAttribute("data-positionId"));



		if (unitName === "Transport" || unitName === "AircraftCarrier") {
			if (positionId !== 118) {
				openContainerPiece = callingElement;
				callingElement.childNodes[0].style.display = "block";
				callingElement.style.zIndex = 30;
				callingElement.parentNode.style.zIndex = 70;
			}
		}
	}

	event.stopPropagation();
}

function pieceDragstart(event, callingElement){
	//only need the placementId to know what is being dragged, server side will handle everything else
	event.dataTransfer.setData("placementId", callingElement.getAttribute("data-placementId"));
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

//button functions---------------------------------------------------
function controlButtonFunction() {
	event.preventDefault();



	event.stopPropagation();
}

function nextPhaseButtonFunction(){
	event.preventDefault();
	if (confirm("Are you sure you want to complete this phase?")) {
		let phpUpdateBoard = new XMLHttpRequest();
		phpUpdateBoard.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				user_feedback.innerHTML = this.responseText;
			}
		};
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
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			user_feedback.innerHTML = this.responseText;
		}
	};
	phpUpdateBoard.open("GET", "backend/game/pieces/pieceMoveUndo.php", true);
	phpUpdateBoard.send();
	event.stopPropagation();
}

function hybridAddMove() {
	event.preventDefault();
	if (confirm("Are you sure you want to use this hybrid option?")) {
		let phpUpdateBoard = new XMLHttpRequest();
		phpUpdateBoard.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				user_feedback.innerHTML = this.responseText;
			}
		};
		phpUpdateBoard.open("GET", "backend/game/hybrid/hybridAddMove.php", true);
		phpUpdateBoard.send();
	}
	event.stopPropagation();
}
function hybridHumanitarian() {
	event.preventDefault();
	if (confirm("Are you sure you want to use this hybrid option?")) {
		let phpUpdateBoard = new XMLHttpRequest();
		phpUpdateBoard.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				user_feedback.innerHTML = this.responseText;
			}
		};
		phpUpdateBoard.open("GET", "backend/game/hybrid/hybridHumanitarian.php", true);
		phpUpdateBoard.send();
	}
	event.stopPropagation();
}
function hybridDisableAircraft() {
	event.preventDefault();
	if (confirm("Are you sure you want to use this hybrid option?")) {
		let phpUpdateBoard = new XMLHttpRequest();
		phpUpdateBoard.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				user_feedback.innerHTML = this.responseText;
			}
		};
		phpUpdateBoard.open("GET", "backend/game/hybrid/hybridDisableAircraft.php", true);
		phpUpdateBoard.send();
	}
	event.stopPropagation();
}
function hybridBankDrain() {
	if (confirm("Are you sure you want use Bank Drain?")) {
		let phpUpdateBoard = new XMLHttpRequest();
		phpUpdateBoard.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				user_feedback.innerHTML = this.responseText;
				if (this.responseText === "Select Island to Drain.") {
					hybridBankState = true;
					unpopIslands();
					popup.style.display = "none";
					control_button.disabled = true;
					phase_button.disabled = true;
					whole_game.style.backgroundColor = "yellow";
				}
			}
		};
		phpUpdateBoard.open("GET", "backend/game/hybrid/hybridBankDrainRequest.php", true);
		phpUpdateBoard.send();
	}
}
function hybridDeletePiece() {
	if (confirm("Are you sure you want use Rods from God?")) {
		let phpUpdateBoard = new XMLHttpRequest();
		phpUpdateBoard.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				user_feedback.innerHTML = this.responseText;
				if (this.responseText === "Select Piece to Delete.") {
					hybridDeletePieceState = true;
					unpopIslands();
					popup.style.display = "none";
					control_button.disabled = true;
					phase_button.disabled = true;
					whole_game.style.backgroundColor = "yellow";
				}
			}
		};
		phpUpdateBoard.open("GET", "backend/game/hybrid/hybridDeletePieceRequest.php", true);
		phpUpdateBoard.send();
	}
}
function hybridDisableAirfield() {
	if (confirm("Are you sure you want use Air Traffic Control Scramble?")) {
		let phpUpdateBoard = new XMLHttpRequest();
		phpUpdateBoard.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				user_feedback.innerHTML = this.responseText;
				if (this.responseText === "Select Airfield to Disable.") {
					hybridDisableAirfieldState = true;
					unpopIslands();
					popup.style.display = "none";
					control_button.disabled = true;
					phase_button.disabled = true;
					whole_game.style.backgroundColor = "yellow";
				}
			}
		};
		phpUpdateBoard.open("GET", "backend/game/hybrid/hybridDisableAirfieldRequest.php", true);
		phpUpdateBoard.send();
	}
}
function hybridNuke() {
	if (confirm("Are you sure you want use Nuclear Strike?")) {
		let phpUpdateBoard = new XMLHttpRequest();
		phpUpdateBoard.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				user_feedback.innerHTML = this.responseText;
				if (this.responseText === "Select Island to Nuke.") {
					hybridNukeState = true;
					unpopIslands();
					popup.style.display = "none";
					control_button.disabled = true;
					phase_button.disabled = true;
					whole_game.style.backgroundColor = "yellow";
				}
			}
		};
		phpUpdateBoard.open("GET", "backend/game/hybrid/hybridNukeRequest.php", true);
		phpUpdateBoard.send();
	}
}

function logout(){
	event.preventDefault();
	window.location.replace("backend/game/logout.php");
	event.stopPropagation();
}
//----------------------------------------------------------------

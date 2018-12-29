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
let battleStates = [unused_attacker, unused_defender, center_attacker, center_defender, used_attacker, used_defender];
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

let selectPosState = false;
let selectPiecesState = false;

let battleAdjacentPlacementIds = [];
//-------------------------------------------------------------------------------------------------------------------

function getBoard(){
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
			} else {
				popup.style.display = "none";
			}
			if (decoded.gamePhase === 0) {
				popupBodyHybridMenu.style.display = "none";
				popupBodyNews.style.display = "block";
			} else {
				popupBodyHybridMenu.style.display = "block";
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
			showDice(parseInt(decoded.gameBattleLastRoll));

			lastBattleMessage.innerHTML = decoded.gameBattleLastMessage;

			actionPopupButton.disabled = decoded.actionPopupButtonDisabled;
			actionPopupButton.innerHTML = decoded.actionPopupButtonText;

			attackButton.disabled = decoded.attack_button_disabled;
			attackButton.innerHTML = decoded.attack_button_text;
			changeSectionButton.disabled = decoded.change_section_button_disabled;
			changeSectionButton.innerHTML = decoded.change_section_button_text;
			if (decoded.gameBattleSection === "selectPos" || decoded.gameBattleSection === "selectPieces") {
				whole_game.style.backgroundColor = "yellow";
				if (decoded.gameBattleSection === "selectPos") {
					selectPosState = true;
					selectPiecesState = false;
					center_attacker.innerHTML = "";  //clear out old battle piece html if not already done
					center_defender.innerHTML = "";
					used_attacker.innerHTML = "";
					used_defender.innerHTML = "";
					unused_attacker.innerHTML = "";
					unused_defender.innerHTML = "";
				} else {
					selectPosState = false;
					selectPiecesState = true;
				}
			} else {
				selectPosState = false;
				selectPiecesState = false;
				whole_game.style.backgroundColor = "black";
			}
			if (decoded.gameBattleSection !== "none" && decoded.gameBattleSection !== "selectPos") {
				document.querySelector("[data-positionId='" + decoded.gameBattlePosSelected + "']").classList.add("selectedPos");
			} else {
				clearSelectedPosition();
			}
			clearSelectedPieces();
			battleAdjacentPlacementIds = decoded.battleAdjacentPlacementIds;
		}
	};
	phpPhaseChange.open("GET", "backend/game/getBoard.php", true);
	phpPhaseChange.send();
}
getBoard();

// Ajax functions --------------------------------------------------------
function ajaxUpdate(){
	//TODO: reevaluate what is actually needed in the update table database format (island could be put somewhere else, rename the piecesSelected)
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText === "TIMEOUT") {
				window.setTimeout("ajaxUpdate();", smallDelay);
			} else {
				let decoded = JSON.parse(this.responseText);
				lastUpdateId = decoded.updateId;
				switch (decoded.updateType) {
					case "logout":
						alert("Teacher has disabled the game...logging out.");  //TODO: don't log out spectators
						logout();
						break;
					case "getBoard":
						getBoard();
						break;
					case "islandOwnerChange":
						ajaxIslandOwnerChange(parseInt(decoded.updatePlacementId), decoded.updateHTML);
						break;
					case "piecePurchase":
						purchased_container.innerHTML += decoded.updateHTML;
						getBoard();
						break;
					case "pieceMove":
						ajaxPieceMove(parseInt(decoded.updatePlacementId), parseInt(decoded.updateNewPositionId), parseInt(decoded.updateNewContainerId));
						break;
					case "pieceRemove":
						ajaxPieceRemove(parseInt(decoded.updatePlacementId));
						break;
					case "piecesSelected":
						unused_attacker.innerHTML = decoded.updateHTML;
						getBoard();
						break;
					case "posSelected":
						unused_defender.innerHTML = decoded.updateHTML;
						getBoard();
						break;
					case "battleMove":
						ajaxBattlePieceMove(parseInt(decoded.updatePlacementId), parseInt(decoded.updateNewPositionId));
						break;
					case "battleRemove":
						ajaxBattlePieceRemove(parseInt(decoded.updatePlacementId));
						break;
					default:
						alert("Error with ajax call, unknown updateType " + decoded.updateType);
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
function ajaxPieceRemove(placementId) {
	//TODO: make all document selectors check for null returns before .removing (see updatePieceDelete() in K2)
	document.querySelector("[data-placementId='" + placementId + "']").remove();
}
function ajaxBattlePieceMove(battlePieceId, battlePieceNewState) {
	let battlePiece = document.querySelector("[data-battlePieceId='" + battlePieceId + "']");
	battleStates[battlePieceNewState-1].append(battlePiece);
}
function ajaxBattlePieceRemove(battlePieceId) {
	document.querySelector("[data-battlePieceId='" + battlePieceId + "']").remove();
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

// Functions for user interaction and piece dragging ----------------------------------------------------------------
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
function waterClick(event, callingElement){
	event.preventDefault();

	if (selectPosState) {
		clearSelectedPosition();
		callingElement.classList.add("selectedPos");
	}

	clearHighlighted();
	unpopIslands();
	closeContainer();



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

		if (selectPosState) {
			clearSelectedPosition();
			callingElement.parentNode.classList.add("selectedPos");
		} else {
			if (selectPiecesState) {
				let placementId = parseInt(callingElement.getAttribute("data-placementId"));
				if (battleAdjacentPlacementIds.includes(placementId)) {
					if (callingElement.classList.contains("selected")) {
						callingElement.classList.remove("selected");
					} else {
						callingElement.classList.add("selected");
					}
				}
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
		}
	}

	clearHighlighted();

	event.stopPropagation();
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
	} else {
		if (selectPosState) {
			clearSelectedPosition();
			callingElement.classList.add("selectedPos");
		}
	}

	clearHighlighted();

	event.stopPropagation();
}

function doubleClick(event, callingElement) {
	event.preventDefault();
	clearHighlighted();
	let positionId = parseInt(callingElement.getAttribute("data-positionId"));
	let phpAvailableMoves = new XMLHttpRequest();
	phpAvailableMoves.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			let positionsArray = JSON.parse(this.responseText);
			for (let g = 0; g < positionsArray.length; g++) {
				document.querySelectorAll("[data-positionId='" + positionsArray[g] + "']")[0].classList.add("highlighted");
			}
		}
	};
	phpAvailableMoves.open("GET", "backend/game/adjacentPositions.php?positionId=" + positionId, true);
	phpAvailableMoves.send();
	event.stopPropagation();
}
function clearHighlighted() {
	let highlighted_things = document.getElementsByClassName("highlighted");
	while (highlighted_things.length) {
		highlighted_things[0].classList.remove("highlighted");
	}
}

//button functions---------------------------------------------------
function controlButtonFunction() {
	event.preventDefault();
	unpopIslands();
	closeContainer();
	switch (control_button.innerHTML) {
		case "Start Battle":
			battleSelectPosStart();
			break;
		case "Done Selecting Pos":
			battleSelectPiecesStart();
			break;
		case "Done Selecting Pieces":
			battleStart();
			break;
		case "Hybrid Tool":
			hybridPopupToggle();
			break;
		default:
			alert("no function");
	}
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
function hybridPopupToggle() {
	if(popup.style.display === "block"){
		popup.style.display = "none";
	}
	else{
		popup.style.display = "block";
	}
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

function battleSelectPosStart() {
	if (confirm("Are you sure you want to start a battle")) {
		let phpUpdateBoard = new XMLHttpRequest();
		phpUpdateBoard.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				user_feedback.innerHTML = this.responseText;
			}
		};
		phpUpdateBoard.open("GET", "backend/game/battles/battleSelectPosStart.php", true);
		phpUpdateBoard.send();
	}
}
function battleSelectPiecesStart() {
	if (document.getElementsByClassName("selectedPos").length === 1) {
		if (confirm("Are you sure this is the position you want?")) {
			let positionId = parseInt(document.getElementsByClassName("selectedPos")[0].getAttribute("data-positionId"));
			let phpUpdateBoard = new XMLHttpRequest();
			phpUpdateBoard.onreadystatechange = function () {
				if (this.readyState === 4 && this.status === 200) {
					user_feedback.innerHTML = this.responseText;
				}
			};
			phpUpdateBoard.open("GET", "backend/game/battles/battleSelectPiecesStart.php?positionId=" + positionId, true);
			phpUpdateBoard.send();
		}
	} else {
		user_feedback.innerHTML = "Did not select a position.";
	}
}
function clearSelectedPosition() {
	let highlighted_things = document.getElementsByClassName("selectedPos");
	while (highlighted_things.length) {
		highlighted_things[0].classList.remove("selectedPos");
	}
}
function clearSelectedPieces() {
	let highlighted_things = document.getElementsByClassName("selected");
	while (highlighted_things.length) {
		highlighted_things[0].classList.remove("selected");
	}
}
function battleStart() {
	if (confirm("Are you sure you selected the correct pieces?")) {
		let allPieces = document.getElementsByClassName("selected");
		let selectedPieces = [];
		for (let x = 0; x < allPieces.length; x++) {
			selectedPieces.push(parseInt(allPieces[x].getAttribute("data-placementId")));
		}
		let phpUpdateBoard = new XMLHttpRequest();
		phpUpdateBoard.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				user_feedback.innerHTML = this.responseText;
			}
		};
		phpUpdateBoard.open("GET", "backend/game/battles/battleStart.php?selectedPieces=" + JSON.stringify(selectedPieces), true);
		phpUpdateBoard.send();
	}
}
function battlePieceClick(event, callingElement) {
	event.preventDefault();
	let battlePieceId = parseInt(callingElement.getAttribute("data-battlePieceId"));
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			user_feedback.innerHTML = this.responseText;
			attackButton.disabled = this.responseText !== "Click Attack to Attack!";
		}
	};
	phpUpdateBoard.open("GET", "backend/game/battles/battlePieceClick.php?battlePieceId=" + battlePieceId, true);
	phpUpdateBoard.send();
	event.stopPropagation();
}
function changeSectionButtonFunction(){
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			user_feedback.innerHTML = this.responseText;
		}
	};
	phpUpdateBoard.open("GET", "backend/game/battles/battleChangeSectionButton.php", true);
	phpUpdateBoard.send();
}
function attackButtonFunction(){
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			user_feedback.innerHTML = this.responseText;
		}
	};
	phpUpdateBoard.open("GET", "backend/game/battles/battleAttackButton.php", true);
	phpUpdateBoard.send();
}
function battleActionPopupButtonClick() {
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			user_feedback.innerHTML = this.responseText;
		}
	};
	phpUpdateBoard.open("GET", "backend/game/battles/battleActionPopupButton.php", true);
	phpUpdateBoard.send();
}


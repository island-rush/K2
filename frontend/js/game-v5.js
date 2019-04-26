console.log("Island Rush Game Javascript");
const phaseNames = ['News', 'Buy Reinforcements', 'Combat', 'Fortify Move', 'Reinforcement Place', 'Hybrid War', 'Round Recap'];
const unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
const whole_game = document.getElementById("whole_game");
const purchased_container = document.getElementById("purchased_container");
const phase_indicator = document.getElementById("phase_indicator");
const red_rPoints_indicator = document.getElementById("red_rPoints_indicator");
const blue_rPoints_indicator = document.getElementById("blue_rPoints_indicator");
const red_hPoints_indicator = document.getElementById("red_hPoints_indicator");
const blue_hPoints_indicator = document.getElementById("blue_hPoints_indicator");
const undo_button = document.getElementById("undo_button");
const control_button = document.getElementById("control_button");
const user_feedback = document.getElementById("user_feedback");
const phase_button = document.getElementById("phase_button");
const battleZonePopup = document.getElementById("battleZonePopup");
const unused_attacker = document.getElementById("unused_attacker");
const unused_defender = document.getElementById("unused_defender");
const used_attacker = document.getElementById("used_attacker");
const used_defender = document.getElementById("used_defender");
const center_attacker = document.getElementById("center_attacker");
const center_defender = document.getElementById("center_defender");
const battleStates = [unused_attacker, unused_defender, center_attacker, center_defender, used_attacker, used_defender];
const attackButton = document.getElementById("attackButton");
const changeSectionButton = document.getElementById("changeSectionButton");
const battle_outcome = document.getElementById("battle_outcome");
const battleActionPopup = document.getElementById("battleActionPopup");
const lastBattleMessage = document.getElementById("lastBattleMessage");
const actionPopupButton = document.getElementById("actionPopupButton");
const dice_image1 = document.getElementById("dice_image1");
const dice_image2 = document.getElementById("dice_image2");
const dice_image3 = document.getElementById("dice_image3");
const dice_image4 = document.getElementById("dice_image4");
const dice_image5 = document.getElementById("dice_image5");
const dice_image6 = document.getElementById("dice_image6");
const dice = [dice_image1, dice_image2, dice_image3, dice_image4, dice_image5, dice_image6];
const popupTitle = document.getElementById("popupTitle");
const newsBodyText = document.getElementById("newsBodyText");
const newsBodySubText = document.getElementById("newsBodySubText");
const popupBodyNews = document.getElementById("popupBodyNews");
const popupBodyHybridMenu = document.getElementById("popupBodyHybridMenu");
const popup = document.getElementById("popup");
const popIsland1 = document.getElementById("special_island1_pop");
const popIsland2 = document.getElementById("special_island2_pop");
const popIsland3 = document.getElementById("special_island3_pop");
const popIsland4 = document.getElementById("special_island4_pop");
const popIsland5 = document.getElementById("special_island5_pop");
const popIsland6 = document.getElementById("special_island6_pop");
const popIsland7 = document.getElementById("special_island7_pop");
const popIsland8 = document.getElementById("special_island8_pop");
const popIsland9 = document.getElementById("special_island9_pop");
const popIsland10 = document.getElementById("special_island10_pop");
const popIsland11 = document.getElementById("special_island11_pop");
const popIsland12 = document.getElementById("special_island12_pop");
const popIslands = [popIsland1, popIsland2, popIsland3, popIsland4, popIsland5, popIsland6, popIsland7, popIsland8, popIsland9, popIsland10, popIsland11, popIsland12];
const gridIsland1 = document.getElementById("special_island1");
const gridIsland2 = document.getElementById("special_island2");
const gridIsland3 = document.getElementById("special_island3");
const gridIsland4 = document.getElementById("special_island4");
const gridIsland5 = document.getElementById("special_island5");
const gridIsland6 = document.getElementById("special_island6");
const gridIsland7 = document.getElementById("special_island7");
const gridIsland8 = document.getElementById("special_island8");
const gridIsland9 = document.getElementById("special_island9");
const gridIsland10 = document.getElementById("special_island10");
const gridIsland11 = document.getElementById("special_island11");
const gridIsland12 = document.getElementById("special_island12");
const gridIslands = [gridIsland1, gridIsland2, gridIsland3, gridIsland4, gridIsland5, gridIsland6, gridIsland7, gridIsland8, gridIsland9, gridIsland10, gridIsland11, gridIsland12];
let openContainerPiece = null;
let openPopupIslandNum = 0;
let pieceDragTimer;
let hybridTimer;
let islandTimer;
let gameCurrentTeam;
let gamePhase;
let gameBattleSection;
let hybridDeletePieceState = false;
let hybridDisableAirfieldState = false;
let hybridBankState = false;
let hybridNukeState = false;
let selectPosState = false;
let selectPiecesState = false;
let battleAdjacentPlacementIds = [];
function getBoard(roll){
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
			popup.style.display = (decoded.news_popped) ? "block" : "none";
			if (decoded.gamePhase === 0) {
				popupBodyHybridMenu.style.display = "none";
				popupBodyNews.style.display = "block";
			} else {
				popupBodyHybridMenu.style.display = "block";
				popupBodyNews.style.display = "none";
			}
			battleZonePopup.style.display = (decoded.battle_popped) ? "block" : "none";
			lastBattleMessage.style.display = "none";
			actionPopupButton.style.display = "none";
			lastBattleMessage.innerHTML = decoded.gameBattleLastMessage;
			actionPopupButton.disabled = decoded.actionPopupButtonDisabled;
			actionPopupButton.innerHTML = decoded.actionPopupButtonText;
			if (roll) {
				rollDice(parseInt(decoded.gameBattleLastRoll));
			} else {
				lastBattleMessage.style.display = "block";
				actionPopupButton.style.display = "block";
				showDice(parseInt(decoded.gameBattleLastRoll));
				battleActionPopup.style.display = (decoded.battle_action_popped) ? "block" : "none";
			}
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
				if (parseInt(decoded.gameBattlePosSelected) > 54 && parseInt(decoded.gameBattlePosSelected) < 75) {
					document.querySelector("[data-positionId='" + decoded.gameBattlePosSelected + "']").parentNode.classList.add("selectedPos");
				} else if (parseInt(decoded.gameBattlePosSelected) > 74) {
					document.querySelector("[data-positionId='" + decoded.gameBattlePosSelected + "']").parentNode.parentNode.classList.add("selectedPos");
				}
			} else {
				clearSelectedPosition();
			}
			clearSelectedPieces();
			battleAdjacentPlacementIds = decoded.battleAdjacentPlacementIds;
			gameCurrentTeam = decoded.gameCurrentTeam;
			gamePhase = parseInt(decoded.gamePhase);
			gameBattleSection = decoded.gameBattleSection;
			battle_outcome.innerHTML = decoded.battleOutcome;
		}
	};
	phpPhaseChange.open("GET", "backend/game/getBoard.php", true);
	phpPhaseChange.send();
}
getBoard(false);
function ajaxUpdate(){
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) {
			if (this.responseText === "TIMEOUT") {
				window.setTimeout("ajaxUpdate();", 50);
			} else {
				let decoded = JSON.parse(this.responseText);
				lastUpdateId = decoded.updateId;
				switch (decoded.updateType) {
					case "logout":
						logout(true);
						break;
					case "getBoard":
						getBoard(false);
						break;
					case "rollBoard":
						getBoard(true);
						break;
					case "islandOwnerChange":
						ajaxIslandOwnerChange(parseInt(decoded.updatePlacementId), decoded.updateHTML);
						break;
					case "piecePurchase":
						purchased_container.innerHTML += decoded.updateHTML;
						getBoard(false);
						break;
					case "pieceMove":
						ajaxPieceMove(parseInt(decoded.updatePlacementId), parseInt(decoded.updateNewPositionId), parseInt(decoded.updateNewContainerId), decoded.updateHTML);
						break;
					case "pieceRemove":
						ajaxPieceRemove(parseInt(decoded.updatePlacementId));
						break;
					case "killRemove":
						ajaxPieceRemove(parseInt(decoded.updatePlacementId));
						user_feedback.innerHTML = decoded.updateHTML;
						break;
					case "piecesSelected":
						unused_attacker.innerHTML = decoded.updateHTML;
						getBoard(false);
						break;
					case "posSelected":
						unused_defender.innerHTML = decoded.updateHTML;
						getBoard(false);
						break;
					case "battleMove":
						ajaxBattlePieceMove(parseInt(decoded.updatePlacementId), parseInt(decoded.updateNewPositionId), decoded.updateHTML);
						break;
					case "battleRemove":
						ajaxBattlePieceRemove(parseInt(decoded.updatePlacementId), decoded.updateHTML);
						break;
					case "updateMoves":
						ajaxUpdateMoves(JSON.parse(decoded.updateHTML));
						break;
					case "lbsmChange":
						ajaxLBSMChange(parseInt(decoded.updatePlacementId), decoded.updateHTML);
						break;
					default:
						alert("Error with ajax call, unknown updateType " + decoded.updateType);
				}
				window.setTimeout("ajaxUpdate();", 50);
			}
		}
	};
	phpUpdateBoard.open("GET", "backend/game/ajaxUpdate.php?gameId=" + gameId + "&lastUpdateId=" + lastUpdateId, true);
	phpUpdateBoard.send();
}
ajaxUpdate();
function ajaxPieceMove(placementId, toPositionId, toContainerId, newTitle) {
	let gamePiece = document.querySelector("[data-placementId='" + placementId + "']");
	gamePiece.setAttribute("title", newTitle);
	let newLocation = (toContainerId === -1) ? document.querySelector("[data-positionId='" + toPositionId + "']") : document.querySelector("[data-placementId='" + toContainerId + "']").firstChild;
	newLocation.append(gamePiece);
	if (toContainerId === -1) {
		newLocation.classList.add("selectedPos");
		setTimeout(function() { newLocation.classList.remove("selectedPos"); }, 2000);
	}
}
function ajaxLBSMChange(placementId, newTeam) {
	let gamePiece = document.querySelector("[data-placementId='" + placementId + "']");
	let oldTeam = newTeam == "Blue" ? "Red" : "Blue";
	gamePiece.classList.remove(oldTeam);
	gamePiece.classList.add(newTeam);
}
function ajaxPieceRemove(placementId) {
	document.querySelector("[data-placementId='" + placementId + "']").remove();
}
function ajaxBattlePieceMove(battlePieceId, battlePieceNewState, battleOutcomeHTML) {
	let battlePiece = document.querySelector("[data-battlePieceId='" + battlePieceId + "']");
	battleStates[battlePieceNewState-1].append(battlePiece);
	battle_outcome.innerHTML = battleOutcomeHTML;
}
function ajaxBattlePieceRemove(battlePieceId, battleOutcomeHTML) {
	document.querySelector("[data-battlePieceId='" + battlePieceId + "']").remove();
	battle_outcome.innerHTML = battleOutcomeHTML;
}
function ajaxIslandOwnerChange(islandNum, newTeamId) {
	let gridIsland = gridIslands[islandNum-1];
	let popIsland = popIslands[islandNum-1];
	let oldTeamId = (newTeamId === "Red") ? "Blue" : "Red";
	gridIsland.classList.remove(oldTeamId);
	gridIsland.classList.add(newTeamId);
	popIsland.classList.remove(oldTeamId);
	popIsland.classList.add(newTeamId);
}
function ajaxUpdateMoves(arrayPiecesMoves) {
	let battleUsedText;
	for (let x = 0; x < arrayPiecesMoves.length; x++) {
		battleUsedText = (arrayPiecesMoves[x][3] === 1) ? "\nUsed in Attack" : "";
		document.querySelector("[data-placementId='" + arrayPiecesMoves[x][0] + "']").setAttribute("title", unitNames[arrayPiecesMoves[x][1]] + "\nMoves: " + arrayPiecesMoves[x][2] + battleUsedText);
	}
}
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
function clearHighlighted() {
	let highlighted_things = document.getElementsByClassName("highlighted");
	while (highlighted_things.length) {
		highlighted_things[0].classList.remove("highlighted");
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
function popupDragenter(event, callingElement) {
	event.preventDefault();
	clearTimeout(islandTimer);
	event.stopPropagation();
}
function positionDragover(event, callingElement){
	event.preventDefault();
	event.dataTransfer.dropEffect = (callingElement.getAttribute("draggable") === "true") ? "none" : "all";
}
function positionDrop(event, callingElement){
	event.preventDefault();
	const placementId = event.dataTransfer.getData("placementId");  //what piece was dropped
	const positionId = parseInt(callingElement.getAttribute("data-positionId"));  //-1 if going into container
	const containerId = parseInt(callingElement.parentNode.getAttribute("data-placementId"));  //-1 if going into position
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) user_feedback.innerHTML = this.responseText;
	};
	phpUpdateBoard.open("GET", "backend/game/pieces/pieceMove.php?placementId=" + placementId + "&positionId=" + positionId + "&containerId=" + containerId, true);
	phpUpdateBoard.send();
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
function pieceTrash(event, callingElement) {
	event.preventDefault();
	const placementId = event.dataTransfer.getData("placementId");  //what piece was dropped
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) user_feedback.innerHTML = this.responseText;
	};
	phpUpdateBoard.open("GET", "backend/game/pieces/pieceTrash.php?placementId=" + placementId, true);
	phpUpdateBoard.send();
	event.stopPropagation();
}
function piecePurchase(unitId){
	event.preventDefault();
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) user_feedback.innerHTML = this.responseText;
	};
	phpUpdateBoard.open("GET", "backend/game/pieces/piecePurchase.php?unitId=" + unitId, true);
	phpUpdateBoard.send();
	event.stopPropagation();
}
function pieceDragstart(event, callingElement){
	if (myTeam === gameCurrentTeam && gameBattleSection === "none" && (gamePhase === 1 || gamePhase === 2 || gamePhase === 3 || gamePhase === 4)) {
		event.dataTransfer.setData("placementId", callingElement.getAttribute("data-placementId"));
	} else {
		event.preventDefault();
	}
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
function rollDice(diceNumber){
	actionPopupButton.style.display = "none";
	lastBattleMessage.style.display = "none";
	battleActionPopup.style.display = "block";
	const timeBetween = 310;
	const numRolls = 9;
	let i;
	let thingy;
	for (i = 1; i < numRolls; i++) {
		let randomRoll = Math.floor(Math.random() * 6) + 1;
		thingy = setTimeout(function () {
			showDice(randomRoll);
		}, (i)*timeBetween);
	}
	thingy = setTimeout(function () {
		showDice(diceNumber);
		actionPopupButton.style.display = "block";
		lastBattleMessage.style.display = "block";
	}, (i)*timeBetween);
}
function gridIslandClick(event, callingElement){
	event.preventDefault();
	if (hybridBankState) {
		if (callingElement.classList[2] !== myTeam) {
			callingElement.classList.add("selectedPos");
			hybridTimer = setTimeout(function() {
				if (confirm("Are you sure you want this island's points for next two turns?")) {
					const islandNum = parseInt(callingElement.getAttribute("data-islandNum"));
					let phpUpdateBoard = new XMLHttpRequest();
					phpUpdateBoard.onreadystatechange = function () {
						if (this.readyState === 4 && this.status === 200) user_feedback.innerHTML = this.responseText;
					};
					phpUpdateBoard.open("POST", "backend/game/hybrid/hybridBankDrain.php?islandNum=" + islandNum, true);
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
				const islandNum = parseInt(callingElement.getAttribute("data-islandNum"));
				let phpUpdateBoard = new XMLHttpRequest();
				phpUpdateBoard.onreadystatechange = function () {
					if (this.readyState === 4 && this.status === 200) user_feedback.innerHTML = this.responseText;
				};
				phpUpdateBoard.open("POST", "backend/game/hybrid/hybridNuke.php?islandNum=" + islandNum, true);
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
		const islandNum = callingElement.getAttribute("data-islandNum");
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
				const placementId = parseInt(callingElement.getAttribute("data-placementId"));
				let phpUpdateBoard = new XMLHttpRequest();
				phpUpdateBoard.onreadystatechange = function () {
					if (this.readyState === 4 && this.status === 200) user_feedback.innerHTML = this.responseText;
				};
				phpUpdateBoard.open("POST", "backend/game/hybrid/hybridDeletePiece.php?placementId=" + placementId, true);
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
				const placementId = parseInt(callingElement.getAttribute("data-placementId"));
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
				const positionId = parseInt(callingElement.parentNode.getAttribute("data-positionId"));
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
		const positionId = parseInt(callingElement.getAttribute("data-positionId"));
		const listairfields = [56, 57, 78, 83, 89, 113, 116, 66, 68];
		if (listairfields.includes(positionId)) {
			hybridTimer = setTimeout(function() {
				if (confirm("Is this the airfield you want to disable?")) {
					let phpUpdateBoard = new XMLHttpRequest();
					phpUpdateBoard.onreadystatechange = function () {
						if (this.readyState === 4 && this.status === 200) user_feedback.innerHTML = this.responseText;
					};
					phpUpdateBoard.open("POST", "backend/game/hybrid/hybridDisableAirfield.php?positionId=" + positionId, true);
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
	const positionId = parseInt(callingElement.getAttribute("data-positionId"));
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
function controlButtonFunction() {
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
}
function nextPhaseButtonFunction(){
	if (confirm("Are you sure you want to complete this phase?")) {
		phase_button.disabled = true;
		generalBackendRequest("backend/game/phaseChange.php");
	}
}
function hybridPopupToggle() {
	popup.style.display = (popup.style.display === "block") ? "none" : "block";
}
function hybridAddMove() {
	if (confirm("Are you sure you want to use this hybrid option?")) generalBackendRequest("backend/game/hybrid/hybridAddMove.php");
}
function hybridHumanitarian() {
	if (confirm("Are you sure you want to use this hybrid option?")) generalBackendRequest("backend/game/hybrid/hybridHumanitarian.php");
}
function hybridDisableAircraft() {
	if (confirm("Are you sure you want to use this hybrid option?")) generalBackendRequest("backend/game/hybrid/hybridDisableAircraft.php");
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
				if (this.responseText === "Select Piece to Destory.") {
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
function logout(teacherForce){
	if (!teacherForce || (teacherForce && myTeam !== "Spec")) {
		if (teacherForce) alert("Teacher has disabled the game...logging out.");
		window.location.replace("backend/game/logout.php");
	}
}
function battleSelectPosStart() {
	if (confirm("Are you sure you want to start a battle")) generalBackendRequest("backend/game/battles/battleSelectPosStart.php");
}
function battleSelectPiecesStart() {
	if (document.getElementsByClassName("selectedPos").length === 1) {
		if (confirm("Are you sure this is the position you want?")) {
			const positionId = parseInt(document.getElementsByClassName("selectedPos")[0].getAttribute("data-positionId"));
			let phpUpdateBoard = new XMLHttpRequest();
			phpUpdateBoard.onreadystatechange = function () {
				if (this.readyState === 4 && this.status === 200) user_feedback.innerHTML = this.responseText;
			};
			phpUpdateBoard.open("GET", "backend/game/battles/battleSelectPiecesStart.php?positionId=" + positionId, true);
			phpUpdateBoard.send();
		}
	} else {
		user_feedback.innerHTML = "Did not select a position.";
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
			if (this.readyState === 4 && this.status === 200) user_feedback.innerHTML = this.responseText;
		};
		phpUpdateBoard.open("GET", "backend/game/battles/battleStart.php?selectedPieces=" + JSON.stringify(selectedPieces), true);
		phpUpdateBoard.send();
	}
}
function battlePieceClick(event, callingElement) {
	event.preventDefault();
	const battlePieceId = parseInt(callingElement.getAttribute("data-battlePieceId"));
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
function generalBackendRequest(fullPath) {
	let phpUpdateBoard = new XMLHttpRequest();
	phpUpdateBoard.onreadystatechange = function () {
		if (this.readyState === 4 && this.status === 200) user_feedback.innerHTML = this.responseText;
	};
	phpUpdateBoard.open("GET", fullPath, true);
	phpUpdateBoard.send();
}

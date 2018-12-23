console.log("Admin Javascript");

function populateGame() {
	if(confirm("ARE YOU SURE YOU WANT TO COMPLETELY RESET THIS GAME?")){
		if(confirm("This will delete all information for the game and set it back to the initial start state of the game.\n\n   ARE YOU SURE YOU WANT TO RESET?")){
			let phpGamePopulate = new XMLHttpRequest();
			phpGamePopulate.open("GET", "backend/admin/gamePopulate.php", true);
			phpGamePopulate.send();

			document.getElementById("populateButton").disabled = true;
			document.getElementById("activeToggle").checked = false;
			// setTimeout(function thingy() {window.location.replace("admin.php");}, 7000);
		}
	}
}


function toggleActive() {
	let phpGamePopulate = new XMLHttpRequest();
	phpGamePopulate.open("GET", "backend/admin/gameToggleActive.php", true);
	phpGamePopulate.send();
}







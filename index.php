<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Island Rush</title>
    <link rel="stylesheet" type="text/css" href="frontend/css/main.css">
    <script>
		console.log("Home Page Javascript");
    </script>
</head>
<body>
<h1>Island Rush Homepage V2.5!!</h1>
<nav>
    <a class="active" href="index.php">Home</a>
	<a href="troubleshoot.html">Troubleshoot</a>
</nav>
<table border="0" cellpadding="30" cellspacing="10">
    <tr>
        <td>
            <H3>Player Login</H3>
            <form name="gameLogin" method="post" id="gameLogin" action="backend/game/loginVerify.php">
                <table border="0" cellpadding="3" cellspacing="1">
                    <tr>
                        <td colspan="2">
                            <div id="formFeedback" class="formError" style="color: red;">
                                <?php
                                if (isset($_GET['err'])) {
                                    switch($_GET['err']) {
                                        case 1:
                                            echo "Teacher has Disabled Game.";
                                            break;
                                        case 2:
                                            echo "Red Team Commander Already Logged In.";
                                            break;
                                        case 3:
                                            echo "Blue Team Commander Already Logged In.";
                                            break;
                                        case 4:
                                            echo "Invalid Team was Selected.";
                                            break;
                                        case 5:
                                            echo "Multiple games exist, inform instructor.";
                                            break;
                                        case 6:
                                            echo "Made request without all required values.";
                                            break;
                                        case 7:
                                            echo "Game does not exist.\nCheck values entered again.";
                                            break;
                                        case 8:
                                            echo "Invalid Game or Admin Request was Made.";
                                            break;
                                        case 9:
                                            echo "Possible Game Timeout or Other Error: Session Lost";
                                            break;
                                        default:
                                            echo "Unknown Error Occured.";
                                    }
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Section</td>
                        <td>
                            <input name="gameSection" type="text" id="gameSection" placeholder="ex: m1a1" autofocus="autofocus" required>
                        </td>
                    </tr>
                    <tr>
                        <td>Teacher Last Name</td>
                        <td>
                            <input name="gameInstructor" type="text" id="gameInstructor" placeholder="ex: Smith" required>
                        </td>
                    </tr>
                    <tr>
                        <td>Team</td>
                        <td>
                            <input type="radio" name="gameTeam" value="Spec" checked> Spectator -> Click this unless told otherwise by instructor.<br>
                            <input type="radio" name="gameTeam" value="Blue"> <blue>Vestrland Commander</blue><br>
                            <input type="radio" name="gameTeam" value="Red"> <red>Zuun Commander</red><br>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><br/><input type="submit" name="Submit" value="Game Login"></td>
                    </tr>
                </table>
            </form>
        </td>
        <td>
            <H3>Admin Login</H3>
            <form name="adminLogin" method="post" id="adminLogin" action="backend/admin/loginVerify.php">
                <table border="0" cellpadding="3" cellspacing="1">
                    <tr>
                        <td>Section</td>
                        <td>
                            <input name="adminSection" type="text" id="adminSection" placeholder="ex: m1a1" autofocus="autofocus" required>
                        </td>
                    </tr>
                    <tr>
                        <td>Teacher Last Name</td>
                        <td>
                            <input name="adminInstructor" type="text" id="adminInstructor" placeholder="ex: Smith" required>
                        </td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td>
                            <input name="adminPassword" type="password" id="adminPassword" required>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><br/><input type="submit" name="Submit" value="Admin Login"></td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>
</body>
</html>

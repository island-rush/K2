<?php
//Session Stuff
session_unset();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Island Rush</title>
    <link rel="stylesheet" type="text/css" href="frontend/css/home.css">
</head>

<body>
<h1>Island Rush Homepage</h1>

<nav>
    <a class="active" href="home.php">Home</a>
    <a href="rulebook.php">Rules</a>
</nav>


<table border="0" cellpadding="30" cellspacing="10">
    <tr>
        <td>
            <H3>Game Login</H3>
            <form name="gameLogin" method="post" id="gameLogin" action="backend/game/loginVerify.php">
                <table border="0" cellpadding="3" cellspacing="1">
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
                            <input type="radio" name="gameTeam" value="Blue"> Vestrland (Blue) Commander<br>
                            <input type="radio" name="gameTeam" value="Red"> Zuun (Red) Commander<br>
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





<script src="frontend/js/home.js"></script>
</body>

</html>

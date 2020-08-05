<?php
session_start();
if (isset($_SESSION["logged"])) {
    header("location: index.php");
}

?>
<html>

<head>
    <title>Yt2Mega - Login</title>
</head>

<body>
    <form action="index.php" method="POST">
        <input type="text" name="user" value="sampras" />
        <input type="text" name="pass" value="ciao" />
        <input type="submit" value="login">
    </form>
</body>

</html>
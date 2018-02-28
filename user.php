<?php

session_start();
if(!$_SESSION['username']) {
    header("location: login.php");
}
/**
 * Created by PhpStorm.
 * User: david
 * Date: 19/02/2018
 * Time: 13:46
 */
?>
<html>
<head></head>
<body>
<p><?php echo $_SESSION['username']; ?></p>
<form action="logout.php">
    <button>
        Logout
    </button>
</form>

<form action="forgotpassword.php">
    <button>
        Logout
    </button>
</form>
</body>
</html>

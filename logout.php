<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 28/02/2018
 * Time: 12:45
 */

require_once("includes/utils.php");

session_start();

$email = $_SESSION['username'];
logger("LOGOUT", $email, "logout.php", "SUCCESS");
$email = "";

session_start();
if(isset($_COOKIE[session_name()])) {
    setcookie(session_name(),'',time()-3600);
}

// unset session id/cookies
$_SESSION["AnonClientSessionID"] = "";
$_SESSION['username'] = "";

$_SESSION = array();

session_destroy();
session_commit();

header("location: login.php");

exit;

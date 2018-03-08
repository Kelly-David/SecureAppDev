<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 15/02/2018
 * Time: 15:21
 */

$GLOBALS['key'] = "ASKSDFNSDFKEISDJAHDLDSDF1235UUUiidfsdf";

// **PREVENTING SESSION HIJACKING**
// Prevents javascript XSS attacks aimed to steal the session ID
ini_set('session.cookie_httponly', 1);

// **PREVENTING SESSION FIXATION**
// Session ID cannot be passed through URLs
ini_set('session.use_only_cookies', 1);

// Uses a secure connection (HTTPS) if possible
ini_set('session.cookie_secure', 1);

define("SECURE", TRUE);

define('DB_SERVER', 'localhost:3306');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'Project2');

/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

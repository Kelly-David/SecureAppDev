<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 28/02/2018
 * Time: 12:55
 */

require_once("db/dbconfig.php");
require_once("includes/utils.php");

session_start();

$token = $email = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($link,$_POST['email']);
    $token = "ranadomtokengenerator";

    debug_to_console($email);
    echo "
    
    <p>
    $token
    </p>
    
    ";

}

if($token == "") {
    echo "
    
    <form action='' method='POST'>
    <p>Enter you email address to reset your password.</p>
    <input type='text' name='email' id='email'>
    <button type='submit'>Reset</button>
    </form>
    
    
    ";
}
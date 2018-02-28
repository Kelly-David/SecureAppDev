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

$token = $email = $email_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($link,$_POST['email']);

    debug_to_console($email);

    // Validate email
    if(empty($email)) {
        $email_err = "Email error";
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Email format error";
    }

    if(empty($email_err)) {

        // Prep SQL statement
        $sql = "SELECT email FROM `user` WHERE email = ?";

        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {

                    // Email is in the db - generate
                    $token = md5(uniqid($email_err, true));
                    $setToken = "UPDATE `user` SET token = ?";
                    if($stmt = mysqli_prepare($link, $setToken)) {

                        mysqli_stmt_bind_param($stmt, "s", $param_token);
                        $param_token = $token;
                        if(mysqli_stmt_execute($stmt)){

                        } else {
                            echo "Please try again later.";
                        }
                    }
                }
            }
        }
    }

    echo "
    
    <p>
    Token: $token
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
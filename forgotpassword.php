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

    if(isset($_POST['token'])) {
        $token = mysqli_real_escape_string($link, $_POST['token']);

        // Prep SQL statement
        $sql = "SELECT token, tokenTime FROM `user` WHERE token = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_token);
            $param_token = $token;
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Token is in the db TODO - check timestamp
                    mysqli_stmt_bind_result($stmt, $r_token, $r_tokenTime);
                    if (mysqli_stmt_fetch($stmt)) {

                        echo "<p> Token is in the database</p>";
                        echo "<p> $r_token";
                        echo "<p> $r_tokenTime";

                        if((getTime() - strtotime($r_tokenTime)) > 300) {
                            echo "<p> Token invalid";
                        }

                    }


                }
                else {
                    echo "<p>Invalid token</p>";
                }
            }
        }

    }
    else {
        $email = mysqli_real_escape_string($link, $_POST['email']);
        // Validate email
        if (empty($email)) {
            $email_err = "Email error";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Email format error";
        }
        if (empty($email_err)) {
            // Prep SQL statement
            $sql = "SELECT email FROM `user` WHERE email = ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $param_email);
                $param_email = $email;
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) == 1) {

                        // Email is in the db - generate
                        $token = md5(uniqid($email_err, true));
                        $setToken = "UPDATE `user` SET token = ?, tokenTime = ? WHERE email = ?";
                        if ($stmt = mysqli_prepare($link, $setToken)) {
                            mysqli_stmt_bind_param($stmt, "sss", $param_token, $param_lastLogin, $param_email);
                            $param_token = $token;
                            // Time
                            $param_lastLogin = getTime();
                            // Set email
                            $param_email = $email;
                            if (mysqli_stmt_execute($stmt)) {
                                // Success
                                echo "
                                    <p>
                                    Token: $token
                                    <form action='' method='POST'>
                                    <label>Email: </label>
                                    <input type='text' name='email'>
                                    <label>Dat of Birth: </label>
                                    <input type='text' name='dob'>
                                    <label>Token: </label>
                                    <input type='text' name='token'>
                                    <label>New Password: </label>
                                    <input type='password' name='password'>
                                    <button type='submit'>Reset</button>
                                    </form>
                                    </p>
                                    ";
                            } else {
                                echo "Please try again later.";
                            }
                        }
                    }
                }
            }

        }
    }

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
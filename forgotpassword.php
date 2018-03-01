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

$token = $email = $password = $password_err = $password_repeat = $dob = $dob_err = $email_err = "";
$token_err = false;

if($_SERVER["REQUEST_METHOD"] == "POST") {

    if(isset($_POST['token'])) {
        $token = mysqli_real_escape_string($link, $_POST['token']);
        $email = mysqli_real_escape_string($link, $_POST['email']);
        $dob = mysqli_real_escape_string($link, $_POST['dob']);
        $password = mysqli_real_escape_string($link, $_POST['password']);
        $password_repeat = mysqli_real_escape_string($link, $_POST['password_repeat']);

        if ($password != $password_repeat) {
            $password_err = "Passwords do not match";
        }

        if(!validate($email, "email", $link)) { $email_err = "Email is invalid"; }

        if (($password_err . $email_err) == "") {

            // Prep SQL statement
            $sql = "SELECT token, tokenTime FROM `user` WHERE token = ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $param_token);
                $param_token = $token;
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        // Token is in the db
                        mysqli_stmt_bind_result($stmt, $r_token, $r_tokenTime);
                        if (mysqli_stmt_fetch($stmt)) {
                            /**  */
                            echo "<p> Token is in the database</p>";
                            echo "<p> $r_token";
                            echo "<p> $r_tokenTime";
                            if ((getTime() - strtotime($r_tokenTime)) > 300) {
                                echo "<p>Token invalid</p>";
                                $token_err = true;
                            }
                            if (!$token_err) {

                                $updatepw = "UPDATE user SET password = ?, token = ? WHERE token = ?";
                                if ($stmt = mysqli_prepare($link, $updatepw)) {
                                    mysqli_stmt_bind_param($stmt, "sss", $param_password, $param_token_reset, $param_token);
                                    $param_password = password_hash($password, PASSWORD_DEFAULT);
                                    $param_token_reset = "";
                                    $param_token = $token;

                                    if (mysqli_stmt_execute($stmt)) {
                                        logger("PASSWORD_RESET", $email);
                                    } else {
                                        echo "Oops! Something went wrong. Please try again later.";
                                    }
                                }
                                mysqli_stmt_close($stmt);
                                mysqli_close($link);
                                // Redirect to logout
                                header("location: login.php");
                            }

                        }


                    } else {
                        echo "<p>Invalid token</p>";
                    }
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
                                echo "<div class='alert alert-info text-center' role='alert'>Password reset token: $token</div>";
                                require_once('includes/passwordTokenReset.php');
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
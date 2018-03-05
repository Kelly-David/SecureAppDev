<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 19/02/2018
 * Time: 13:29
 */
require_once("db/dbconfig.php");
require_once("includes/utils.php");

$can_authenticate = true;

session_start();

require_once ("includes/client.php");
// Define variables

$email = $password = "";
$email_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

    // Form params
    $email = mysqli_real_escape_string($link,$_POST['email']);
    $password = mysqli_real_escape_string($link,$_POST['password']);

    // VALIDATE THE EMAIL
    if (emailIsValid($email)) {
        if(!emailRegistered($email, $link)) {
            logger("LOGIN", $anonClientID, "login.php", "DENY", $email);
            $email_err = htmlspecialchars( $email, ENT_QUOTES) .  " is not registered in the system.";
        }
    }
    // AUTHENTICATE THE PASSWORD
    if(empty($email_err)) {
        // Prep SQL statement
        $sql = "SELECT username, password, lastLogin, attempt  FROM `user` WHERE email = ? ";
        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = _crypt($email);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if ((mysqli_stmt_num_rows($stmt) == 1)) {
                    mysqli_stmt_bind_result($stmt, $myusername, $hashed_password, $llogin, $att);
                    if (mysqli_stmt_fetch($stmt)) {
                        $att = $att;
                        $time = strtotime($llogin);
                        $current_time = getTime();
                        // Check the lockout time and attempts
                        if ((($current_time - $time) < 300) && ($att == 3)) { // 5 minutes
                            logger("LOGIN", $anonClientID, "login.php", "DENY", "Locked Out");
                            $login_err = "Account blocked - try again later";
                        }
                        elseif(empty($login_err)) {
                            if(password_verify($password, $hashed_password)) {
                                // Password is correct - user is authenticated
                                $update = "UPDATE user SET lastLogin = ?, attempt = ? WHERE email = ?";
                                if($stmt = mysqli_prepare($link, $update)) {
                                    mysqli_stmt_bind_param($stmt, "sss", $param_lastLogin, $param_attempt, $param_username);
                                    $param_lastLogin = getTime();
                                    $param_attempt = 0;
                                    $param_username = _crypt($email);
                                    if(mysqli_stmt_execute($stmt)){
                                        logger("LOGIN", $anonClientID, "login.php", "SUCCESS", $email);
                                        // Reset the client login attempts to 0
                                        clientAttemptReset($anonClientID, $link);
                                        session_start();
                                        $_SESSION['username'] = $email;
                                        header("location: user.php");
                                    }
                                    else {
                                        logger("QUERY ERROR", $anonClientID, "login.php", "EXCEPTION");
                                        echo "Please try again later.";
                                    }
                                }
                            }
                            else {
                                $password_err = "Email "  . htmlspecialchars($email, 3) . " and password combination invalid";
                                if ($att < 3) {
                                    // Failed login - Log the attempt against the user and session
                                    $user_sql = "UPDATE user SET attempt = attempt + 1 WHERE email = ?";
                                    if($stmt = mysqli_prepare($link, $user_sql)) {
                                        mysqli_stmt_bind_param($stmt, "s", $param_username);
                                        $param_username = _crypt($email);
                                        if(mysqli_stmt_execute($stmt)){
                                            logger("LOGIN", $anonClientID, "login.php", "DENY", $password);
                                        } else {
                                            logger("QUERY ERROR", $anonClientID, "login.php", "EXCEPTION");
                                            echo "Please try again later.";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    // Update the session of the failed login attempt [prevent brute force].
    if(!empty($email_err) || !empty($password_err) || !empty($login_err)) {
        $email_err = clientAttemptQuery($anonClientID, $link, $email);
    }
}

if($can_authenticate) {
    require_once("includes/loginForm.php");
} else {
    require_once("includes/lockout.php");
}
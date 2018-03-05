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

$can_authenticate = true;

require_once ('includes/client.php');


$token = $email = $password  = $password_repeat = $dob = $user = "";
$token_err = $password_err = $dob_err = $email_err = $user_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

    if(isset($_POST['token']) && $_SESSION['email']) {

        $email = $_SESSION['email'];
        $token = mysqli_real_escape_string($link, $_POST['token']);
        $dob = mysqli_real_escape_string($link, $_POST['dob']);
        $password = mysqli_real_escape_string($link, $_POST['password']);
        $password_repeat = mysqli_real_escape_string($link, $_POST['password_repeat']);

        // Passwords need to match
        if ($password != $password_repeat) {
            logger("PASSWORD_RESET", $anonClientID, "forgotpassword.php", "DENY", $password);
            $password_err = "Passwords do not match";
            echo "<div class='alert alert-danger text-center' role='alert'> $password_err </div>";

        }
        // Passwords also need to be the correct format
        if(empty($user = getUser($email, $link))) {
            $user_err = "Invalid parameters";
            logger("PASSWORD_RESET", $anonClientID, "forgotpassword.php", "DENY",$password);
            echo "<div class='alert alert-danger text-center' role='alert'>$user_err</div>";
        } else {
            // Validate the password - if false password is not the correct format
            if(!passwordComplexity($password, $user, $email)) {
                logger("PASSWORD_RESET", $anonClientID, "forgotpassword.php", "DENY",$password);
                $password_err = "Invalid password. Re-enter.";
                echo "<div class='alert alert-danger text-center' role='alert'>$password_err</div>";
            }
        }

        // Re-validate the email in the session - just to be sure!
        if(!validate($email, "email", $link)) {
            logger("PASSWORD_RESET", $anonClientID, "forgotpassword.php", "DENY",$email);
            $email_err = "Email is invalid";
            echo "<div class='alert alert-danger text-center' role='alert'>$email_err</div>";
        }

        // No errors - continue - validate the token
        if (($password_err . $email_err . $token_err) == "") {
            // Prep SQL statement
            $sql = "SELECT token, tokenTime FROM `user` WHERE token = ? AND dob = ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $param_token, $param_dob);
                $param_token = $token;
                $param_dob = _crypt($dob);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_store_result($stmt);
                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        // Token is in the database
                        mysqli_stmt_bind_result($stmt, $r_token, $r_tokenTime);
                        if (mysqli_stmt_fetch($stmt)) {
                            $currentTime = getTime();
                            // Has token expired...
                            $differenceInSeconds = strtotime($currentTime) - strtotime($r_tokenTime);
                            if ($differenceInSeconds >= 300) {
                                logger("INVALID TOKEN", $anonClientID, "forgotpassword.php", "DENY", $token);
                                $token_err = "Expired token";
                            }
                            // Token is valid
                            if(empty($token_err)) {
                                $updatepw = "UPDATE user SET password = ?, token = ? WHERE token = ?";
                                if ($stmt = mysqli_prepare($link, $updatepw)) {
                                    mysqli_stmt_bind_param($stmt, "sss", $param_password, $param_token_reset, $param_token);
                                    $param_password = password_hash($password, PASSWORD_DEFAULT);
                                    // Clear the existing token
                                    $param_token_reset = "";
                                    $param_token = $token;
                                    if (mysqli_stmt_execute($stmt)) {
                                        logger("PASSWORD_RESET", $anonClientID, "forgotpassword.php", "SUCCESS", $email);
                                        mysqli_stmt_close($stmt);
                                        mysqli_close($link);
                                        // Redirect to logout - clears session variables
                                        header("location: logout.php");
                                    } else {
                                        logger("QUERY ERROR", $anonClientID, "login.php", "EXCEPTION");
                                        echo "Oops! Something went wrong. Please try again later.";
                                    }
                                }
                            }
                        }
                    } else {
                        // Token is invalid
                        logger("INVALID TOKEN", $anonClientID, "forgotpassword.php", "DENY", $token);
                        $token_err = "Invalid parameters";
                        echo "<div class='alert alert-danger text-center' role='alert'>$token_err</div>";
                    }
                }
            }
        }
    }
    else {
        // POST param
        $email = mysqli_real_escape_string($link, $_POST['email']);
        // Validate the email (is a valid email format and exists in the database)
        if(!validate($email, "email", $link)) {
            logger("PASSWORD_RESET", $anonClientID, "forgotpassword.php", "DENY", $email);
            $email_err = "Invalid email. Re-enter.";
            echo "<div class='alert alert-danger text-center' role='alert'>$email_err</div>";
        }
        else {
            // Email is in the db
            // Generate a random token using the user's email
            $token = md5(uniqid($email, true));
            $setToken = "UPDATE `user` SET token = ?, tokenTime = ? WHERE email = ?";
            if ($stmt = mysqli_prepare($link, $setToken)) {
                mysqli_stmt_bind_param($stmt, "sss", $param_token, $param_lastLogin, $param_email);
                $param_token = $token;
                // Time
                $param_lastLogin = getTime();
                // Set email
                $param_email = _crypt($email);
                if (mysqli_stmt_execute($stmt)) {
                    logger("GENERATE TOKEN", $anonClientID, "forgotpassword.php", "SUCCESS");
                    // Success - bind the email to the session
                    $_SESSION['email'] = $email;
                    // Display the token
                    echo "<div class='alert alert-info text-center' role='alert'>Password reset token: $token</div>";
                } else {
                    logger("QUERY ERROR", $anonClientID, "login.php", "EXCEPTION");
                    echo "Please try again later.";
                }
            }
        }
    }

}

// Update the session of the failed reset attempt [prevent brute force].
if(!empty($email_err)) {
    $email_err = clientAttemptQuery($anonClientID, $link, $email);
}

if($can_authenticate) {
    // No token set - display the form to request token using email.
    if($token == "") {
        require_once ("includes/requestToken.php");
    }
    else {
        // Display the password reset with token form
        require_once('includes/passwordTokenReset.php');
    }
} else {
    require_once ('includes/lockout.php');
}
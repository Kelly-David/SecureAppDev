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

require_once ("includes/client.php");
// Define variables

$email = $password = "";
$email_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

    // Time
    $server_time = new DateTime();
    $server_time = $server_time->format('Y-m-d H:i:s');

    // Form params
    $email = mysqli_real_escape_string($link,$_POST['email']);
    $password = mysqli_real_escape_string($link,$_POST['password']);

    debug_to_console( $email );
    debug_to_console( $password );


    // VALIDATE THE EMAIL
    if ((!empty($email)) && (filter_var($email, FILTER_VALIDATE_EMAIL))) {

        // Prep SQL statement
        $sql = "SELECT email FROM `user` WHERE email = ?";

        if($stmt = mysqli_prepare($link, $sql)) {

            mysqli_stmt_bind_param($stmt, "s", $param_email);

            $param_email = $email;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (!(mysqli_stmt_num_rows($stmt) == 1)) {

                    $email_err = htmlspecialchars( $email, ENT_QUOTES) .  " is not registered in the system.";
                }

                debug_to_console("Email is registered.");

            }
        }
    }

    // AUTHENTICATE THE PASSWORD
    if(empty($email_err)) {

        // Prep SQL statement
        $sql = "SELECT username, password, lastLogin, attempt  FROM `user` WHERE email = ? ";

        if($stmt = mysqli_prepare($link, $sql)) {

            mysqli_stmt_bind_param($stmt, "s", $param_email);

            $param_email = $email;
            debug_to_console($param_email);


            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if ((mysqli_stmt_num_rows($stmt) == 1)) {

                    mysqli_stmt_bind_result($stmt, $myusername, $hashed_password, $llogin, $att);

                    if (mysqli_stmt_fetch($stmt)) {

                        $time = strtotime($llogin);

                        $curtime = time();

                        if ((($curtime - $time) < 300) && ($att == 3)) {     // 5 mins
                            $login_err = "Account blocked - try again later";
                        }

                        if(empty($login_err)) { // Proceed if no login error

                            if(password_verify($password, $hashed_password)) {

                                // Password is correct
                                $update = "UPDATE user SET lastLogin = ?, attempt = ? WHERE email = ?";
                                if($stmt = mysqli_prepare($link, $update)) {

                                    mysqli_stmt_bind_param($stmt, "sss", $param_lastLogin, $param_attempt, $param_username);
                                    $param_lastLogin = $server_time;
                                    $param_attempt = 0;
                                    $param_username = $email;
                                    if(mysqli_stmt_execute($stmt)){

                                    } else {
                                        echo "Please try again later.";
                                    }
                                    logger("LOGIN", $email);
                                    session_start();
                                    $_SESSION['username'] = $email;
                                    header("location: user.php");
                                }

                            }
                            elseif($att < 3) {
                                // Failed login - Log the attempt against the user and session

                                $user_sql = "UPDATE user SET attempt = attempt + 1 WHERE username = ?";

                                if($stmt = mysqli_prepare($link, $user_sql)) {

                                    mysqli_stmt_bind_param($stmt, "s", $param_username);

                                    $param_username = $email;

                                    if(mysqli_stmt_execute($stmt)){

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
    }

    if($email_err || $password_err) {

        $session_sql = "UPDATE clientSession SET Counter = Counter + 1, Tstamp = NOW() WHERE SessionID = '$anonClientID'";

        $result = mysqli_query($link,$session_sql);

        if (!$result) {

            die('Could not query:' . mysql_error());
        }

        $email_err = 'Email ' . htmlspecialchars($email, 3) . ' and password combination invalid';

    }
}




?>
<?php
if($can_authenticate) {
    require_once("includes/loginForm.php");
} else {
    require_once("includes/lockout.php");
}
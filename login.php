<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 19/02/2018
 * Time: 13:29
 */
require_once("db/dbconfig.php");
require_once("includes/utils.php");
session_start();

// Define variables

$email = $password = "";
$email_err = $password_err = "";

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
    if((empty($email_err)) && !empty($password)) {

        // Prep SQL statement
        $sql = "SELECT username, password, lastLogin, attempt  FROM `user` WHERE email = ? ";

        if($stmt = mysqli_prepare($link, $sql)) {

            mysqli_stmt_bind_param($stmt, "s", $param_email);

            $param_email = $email;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (!(mysqli_stmt_num_rows($stmt) == 1)) {

                    //ok
                    //bind the result
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
                                $update = "UPDATE user SET lastLogin = ?, attempt = ? WHERE username = ?";
                                if($stmt = mysqli_prepare($link, $update)) {

                                    mysqli_stmt_bind_param($stmt, "sss", $param_lastLogin, $param_attempt, $param_username);
                                    $param_lastLogin = $server_time;
                                    $param_attempt = 0;
                                    $param_username = $email;
                                    if(mysqli_stmt_execute($stmt)){

                                    } else {
                                        echo "Please try again later.";
                                    }
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

                                $session_sql = "UPDATE clientSession SET Counter = Counter + 1, Tstamp = NOW() WHERE SessionID = '$anonClientID'";

                                $result = mysqli_query($link,$session_sql);

                                if (!$result) {

                                    die('Could not query:' . mysql_error());
                                }

                                $username_err = 'Username ' . htmlspecialchars($email, 3) . ' and password combination invalid';
                            }
                        }
                    }

                }

            }

        }
    }
}




?>
<!DOCTYPE html>
<html>

<head>
    <?php include("includes/styles.php"); ?>
    <title>Login</title>
</head>

<body>
<div class="container-fluid">
    <div class="row" style="margin: 1rem 0 0 0">
        <div class="col-lg-2">
            <p></p>
        </div>
        <div class="col-lg-8 col-sm-12">
            <div class="card">
                <div class="card-header">Login
                </div>
                <div class="card-body">
                    <form action="" method="post" autocomplete="off" >
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control form-control-sm" id="email" name="email" placeholder="Enter an email" >
                            <small id="emailAlert" class="form-text text-muted float-right"><?php echo $email_err; ?></small>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control form-control-sm" id="password" name="password" placeholder="Enter a password" >
                            <span>
                                    <small id="passwordAlert" class="form-text text-muted float-right"><?php echo $password_err; ?></small>
                                </span>
                        </div>
                        <br>
                        <small>
                            <a href="register.php">New user?</a>
                        </small>
                        <button type="submit" id="submit" class="btn btn-primary btn-sm float-right"><i class="fa fa-sign-in" aria-hidden="true"></i> Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include("includes/js.php"); ?>
</div>
</body>
</html>

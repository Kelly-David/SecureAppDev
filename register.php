<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 15/02/2018
 * Time: 15:13
 */

require_once("db/dbconfig.php");
require_once("includes/utils.php");
session_start();

$email_err = $user_err = $dob_err = $password_err = "";
$email = $user = $dob = $password = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

    // Form params
    $user = mysqli_real_escape_string($link,$_POST['user']);
    $password = mysqli_real_escape_string($link,$_POST['password']);
    $email = mysqli_real_escape_string($link,$_POST['email']);
    $dob = mysqli_real_escape_string($link,$_POST['dob']);

    // Testing
    debug_to_console( $user );
    debug_to_console( $password );
    debug_to_console( $email );
    debug_to_console( $dob );

    // Validate the email
    if ((!empty($email)) && (filter_var($email, FILTER_VALIDATE_EMAIL))) {

        $key = "ASKSDFNSDFKEISDJAHDLDSDF1235UUUiidfsdf";

        // Prep SQL statement
        $sql = "SELECT AES_DECRYPT(email, $key) FROM user WHERE email = AES_ENCRYPT(?, $key)";

        if($stmt = mysqli_prepare($link, $sql)) {

            mysqli_stmt_bind_param($stmt, "s", $param_email);

            $param_email = $email;

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $retrieved_email);

                    if(mysqli_stmt_fetch($stmt)) {
                        //Debugging
                        debug_to_console( $retrieved_email );

                        if($param_email == $retrieved_email) {
                            $email_err = "Email registered in the system. Re-enter or <a href='login.php'>login</a>.";
                        }
                    }
                }
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }

    }
    else {
        $email_err = "Email invalid";
    }

    if (!empty($password) && !(empty($user)) && !empty($email) && !empty($dob)) {

        $key = "ASKSDFNSDFKEISDJAHDLDSDF1235UUUiidfsdf";

        debug_to_console($key);

        $sql = "INSERT INTO user (`username`, `password`, `email`, `dob`) VALUES ($user, $password, $email, $dob)";

        $result = mysqli_query($link,$sql);

        if (!$result) {
            die('Could not query:' . mysql_error());
        } // OK
    }



} // POST REQ END


?>
<!DOCTYPE html>
<html>

<head>
    <?php include("includes/styles.php"); ?>
    <title>Register</title>
</head>

<body>
<div class="container-fluid">
    <div class="row" style="margin: 1rem 0 0 0">
        <div class="col-lg-2">
            <p></p>
        </div>
        <div class="col-lg-8 col-sm-12">
            <div class="card">
                <div class="card-header">Register
                    <span class="float-right">
                            <small class="form-text text-muted">
                                <a href="" data-toggle="collapse" data-target="#demo">
                                    <i class="fa fa-info" aria-hidden="true">&nbsp;</i>
                                </a>
                            </small>
                            <span>
                </div>
                <div class="card-body">
                    <form action="" method="post" autocomplete="off" >
                        <div class="form-group">
                            <label for="user">Name</label>
                            <input type="text" class="form-control form-control-sm" id="user" name="user" placeholder="Enter user" >
                            <small id="userAlert" class="form-text text-muted float-right"><?php echo $user_err; ?></small>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control form-control-sm" id="email" name="email" placeholder="Enter an email" >
                            <small id="emailAlert" class="form-text text-muted float-right"><?php echo $email_err; ?></small>
                        </div>
                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" class="form-control form-control-sm" id="dob" name="dob" placeholder="Enter dob" >
                            <small id="dobAlert" class="form-text text-muted float-right"><?php echo $dob_err; ?></small>
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
                            <a href="login.php">Already registered?</a>
                        </small>
                        <button type="submit" id="submit" class="btn btn-primary btn-sm float-right"><i class="fa fa-sign-in" aria-hidden="true"></i> Submit</button>
                    </form>
                </div>
                <div class="card-footer">
                    <div id="demo" class="collapse">
                        <small class="form-text text-muted">
                            <b>Username: </b> Enter a username.<br>
                            <b>Email: </b> Enter a valid email address.<br>
                            <b>Date of Birth: </b> Specify date of birth.<br>
                            <b>Password: </b> Minimum length is 6. Must contain at least: 1 uppercase char, 1 number.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include("includes/js.php"); ?>
</div>
</body>
</html>

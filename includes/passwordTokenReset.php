<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 01/03/2018
 * Time: 11:44
 */

echo "
<!DOCTYPE html>
<html>
<head>";
include("includes/styles.php");
echo "
    <title>Password Reset</title>
</head>
<body>
<div class='container-fluid'>
    <div class='row' style='margin: 1rem 0 0 0'>
        <div class='col-lg-3'>
            <p></p>
        </div>
        <div class='col-lg-6 col-sm-12'>
            <div class='card'>
                <div class='card-header'>
                    Password Reset
                </div>
                <div class='card-body'>
                    <form action='' method='post'>
                        <div class='form-group'>
                            <label for='dob'>Date of Birth</label>
                            <input type='date' class='form-control form-control-sm' id='dob' name='dob' placeholder='Enter dob' >
                            <small id='dobAlert' class='form-text text-muted float-right'><?php echo $dob_err; ?></small>
                        </div>
                        <div class='form-group'>
                            <label for='token'>Token</label>
                            <input type='text' class='form-control form-control-sm' id='token' name='token' placeholder='Enter token' >
                            <small id='tokenAlert' class='form-text text-muted float-right'><?php echo $token_err; ?></small>
                        </div>
                        <div class='form-group'>
                            <label for='password'>Password</label>
                            <input type='password' class='form-control form-control-sm' id='password' name='password' placeholder='Enter a password' >
                            <small id='passwordAlert' class='form-text text-muted float-right'><?php echo $password_err; ?></small>
                        </div>
                        <div class='form-group'>
                            <label for='password_repeat'>Password</label>
                            <input type='password' class='form-control form-control-sm' id='password_repeat' name='password_repeat' placeholder='Re-enter password' >
                            <small id='passwordAlert' class='form-text text-muted float-right'><?php echo $password_err; ?></small>
                        </div>
                        <br>
                        <button type='submit' id='submit' class='btn btn-primary btn-sm float-right'><i class='fa fa-sign-in' aria-hidden='true'></i> Reset</button>
                    </form>
                </div>
                <div class='card-footer'>
                </div>
            </div>
        </div>
    </div>
    ";
 include("includes/js.php");
 echo "
</div>
</body>
</html>
";
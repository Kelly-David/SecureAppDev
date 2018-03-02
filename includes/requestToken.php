<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 01/03/2018
 * Time: 16:13
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
                <p>Enter your email to reset your password.</p>
                    <form action='' method='post'>
                        <div class='form-group'>
                            <label for='email'>Email</label>
                            <input type='email' class='form-control form-control-sm' id='email' name='email' placeholder='Enter an email' >
                            <small id='emailAlert' class='form-text text-muted float-right'></small>
                        </div>
                        <br>
                        <button type='submit' id='submit' class='btn btn-primary btn-sm float-right'><i class='fa fa-sign-in' aria-hidden='true'></i> Submit</button>
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
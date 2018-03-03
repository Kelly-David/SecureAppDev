<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 19/02/2018
 * Time: 14:43
 */
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
        <div class="col-lg-3">
            <p></p>
        </div>
        <div class="col-lg-6 col-sm-12">
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
                            <a href="register.php">New user?</a><br>
                            <a href="forgotpassword.php">Password Reset</a>
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

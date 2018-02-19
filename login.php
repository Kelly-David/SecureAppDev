<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 19/02/2018
 * Time: 13:29
 */
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
                <div class="card-header">Login
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

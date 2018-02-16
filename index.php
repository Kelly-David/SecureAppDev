<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 15/02/2018
 * Time: 15:13
 */


?>


<?php

$login = "";

if( isset( $_POST[ 'create_db' ] ) ) {

    $DBMS = 'MySQL';

    if( $DBMS == 'MySQL' ) {
        include_once 'db/mysql.php';
    }
    else {
        die();
    }
}

?>


<!DOCTYPE html>
<html>

<head>
    <?php include("includes/styles.php"); ?>
    <title>Setup</title>
</head>

<body>
<div class="container-fluid">
    <div class="row" style="margin: 1rem 0 0 0">
        <div class="col-sm-3">
            <p></p>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    Setup Database
                </div>
                <div class="card-body">

                    <!-- Create db button -->
                    <form action="#" method="post" class="text-center">
                        <input name="create_db" type="submit" value="Build Database" class="btn btn-info btn-sm">
                    </form>

                    <?php echo $login; ?>

                </div>
                <div class="card-footer">
                    <div id="demo" class="collapse">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include("partials/js.php"); ?>
</div>
</body>

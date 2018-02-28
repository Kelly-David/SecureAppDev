<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 16/02/2018
 * Time: 14:14
 */
date_default_timezone_set("Europe/Dublin");

// Testing function: output to console
function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}


// Get time
function getTime() {
    // Time
    $server_time = new DateTime();
    $server_time = $server_time->format('Y-m-d H:i:s');
    return $server_time;
}

function emailRegistered($email, $link) {
    $valid = false;
    $sql = "SELECT email FROM `user` WHERE email = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // Email exists in the db
                $valid = true;
            }
        }
    }
    return $valid;
}

function emailIsValid($email) {
    $valid= false;
    if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $valid = true;
    }
    return $valid;
}

function validate($param, $case, $link) {
    $valid = false;
    switch ($case) {
        case "email":
            if(emailRegistered($param, $link) && emailIsValid($param)) { $valid = true;}
            break;
        case "password":

            break;
        default:
            break;
    }
    return $valid;
}
<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 16/02/2018
 * Time: 14:14
 */

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
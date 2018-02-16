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
<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 15/02/2018
 * Time: 15:20
 */

include("../includes/utils.php");

$DBMS = 'MySQL';

$DB_Param = array();
$DB_Param['server'] = 'localhost:3306';
$DB_Param['database'] = 'part2';
$DB_Param['user'] = 'root';
$DB_Param['password'] = '';

$link = mysqli_connect($DB_Param['server'],$DB_Param['user'],$DB_Param['password']);
// Check connection
if($link === false) {
    die('Error: Could not connect to db :' . mysqli_connect_error());
}

// Drop the db if is already exists
$sql_purge = "DROP DATABASE IF EXISTS {$DB_Param['database']};";
if( !@mysqli_query($link,$sql_purge)) {
    debug_to_console("Could not drop database");
}

// Create the db
$sql_create = "CREATE DATABASE {$DB_Param['database']};";
if( !@mysqli_query($link,$sql_create)) {
    debug_to_console("Could not create database");
}

// Use the database
if( !@((bool)mysqli_query($link, "USE " . $DB_Param['database'])) ) {
    debug_to_console( 'Could not connect to database.' );
}

// Create the user table
$sql_create_tb = "CREATE TABLE user(
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(50),
    password VARCHAR(200),
    email VARCHAR(200),
    dob VARCHAR(100),
    lastLogin TIMESTAMP,
    state BOOLEAN NOT NULL DEFAULT 1,
    attempt INT(10) NOT NULL DEFAULT 0,
	sessionID varchar(33),
	token varchar(50),
	tokenTime TIMESTAMP,
    PRIMARY KEY (id)
);";
if( !@mysqli_query($link,$sql_create_tb)) {
    debug_to_console("Could not create user table");
}

$sql_create_session_tb = "CREATE TABLE clientSession(
    SessionID varchar(33) NOT NULL,
	Counter int(11) NOT NULL,
	Tstamp datetime NOT NULL,
    PRIMARY KEY (SessionID)
);";
if( !@mysqli_query($link,$sql_create_session_tb)) {
    debug_to_console("Could not create user table");
}

// Done - redirect
$login = "<a href='register.php'>login</a>";
header("location: register.php");

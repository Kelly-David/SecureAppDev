<?php
/**
 * @file client.php
 * Check to see if is a new client. If new client; create a client ref in the db.
 * Else check check client attempts; if valid number of attempts (<3) - permit auth; else lockout.
 * Created by PhpStorm.
 * User: david
 * Date: 19/02/2018
 * Time: 14:31
 */
date_default_timezone_set("Europe/Dublin");

// Get IP address
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
// Get user agent
$browser = $_SERVER['HTTP_USER_AGENT'];

// Build client ID string
$anonClientID = (string)$browser. (string)$ip;

// Hash and store client ID
$anonClientID = md5($anonClientID);
$_SESSION["AnonClientSessionID"]=$anonClientID;

// Check if we have seen this client
$sql = "SELECT `Counter`,`Tstamp` FROM `clientSession` WHERE `SessionID` = '$anonClientID'";
$objDateTime = new DateTime('NOW');
$query = mysqli_query($link,$sql);

if ($query->num_rows == 0) {  // New client
    logger("CREATE CLIENT", $anonClientID, "client.php", "SUCCESS");
    $sql = "INSERT INTO `clientSession` (`SessionID`, `Counter`, `Tstamp`) VALUES ('$anonClientID', '0', NOW())";

    if (!mysqli_query($link,$sql)) {
        logger("QUERY ERROR", $anonClientID, "client.php", "EXCEPTION");
        die('Error: ' . mysqli_error($con));
    } // Inserted
} else { // We have seen this client
    $sql = "SELECT `Counter` FROM `clientSession` WHERE `SessionID` = '$anonClientID'";
    $result = mysqli_query($link,$sql);
    if (!$result) {
        logger("QUERY ERROR", $anonClientID, "client.php", "EXCEPTION");
        die('Could not query:' . mysql_error());
    } else { // OK
        $counter = ($result->fetch_row()[0]);  // get the counter
        if ($counter > 2) // 3 login attempts
        {
            $sql = "SELECT `Tstamp` FROM `clientSession` WHERE `SessionID` = '$anonClientID'";
            $result = mysqli_query($link,$sql);

            if (!$result) {
                logger("QUERY ERROR", $anonClientID, "client.php", "EXCEPTION");
                die('Could not query:' . mysql_error());
            } else {
                // get the last login attempt time to determine if a 5 min lockout should be enforced
                $lastLoginAttemptTime = ($result->fetch_row()[0]);
            }
            $currentTime = date('Y-m-d H:i:s');
            $differenceInSeconds = strtotime($currentTime) - strtotime($lastLoginAttemptTime);
            if((int)$differenceInSeconds <= 300) { // 5 minute lockout
                logger("CAN AUTH", $anonClientID, "client.php", "DENY");
                // Client not permitted to attempt login
                $can_authenticate = false;
            } else
            { // Display Login
                logger("CAN AUTH", $anonClientID, "client.php", "SUCCESS");
                //reset the counter as 3 minutes has passed.
                $sql = "UPDATE `clientSession` SET `Counter`=0, `Tstamp` = NOW() WHERE `SessionID` = '$anonClientID'";
                $result = mysqli_query($link,$sql);
                if (!$result) {
                    logger("QUERY ERROR", $anonClientID, "client.php", "EXCEPTION");
                    die('Could not query:' . mysql_error());
                } // OK
            }
        }
        // Client can proceed to attempt login
    }
}
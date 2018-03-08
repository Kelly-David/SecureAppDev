<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 05/03/2018
 * Time: 13:34
 */
require_once ("includes/utils.php");
session_start();
// Check user is authenticated
if(!$_SESSION['username']) {
    header("location: logout.php");
}

echo "<html>
        <head>
        <title>Log</title>";
include("includes/styles.php");
echo "  </head>
      <body><div class='container-fluid'><h4>Activity Log</h4>";

$log = csv_to_array('test.csv');


echo "<table class='table table-sm table-responsive' style='font-size: .8rem'>";
echo "<thead>";
echo "<tr>";
echo "<th>Time</th>";
echo "<th>Action</th>";
echo "<th>Session ID</th>";
echo "<th>Source</th>";
echo "<th>Result</th>";
echo "<th>Payload</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

foreach ($log as $row) {
    echo "<tr>";
    foreach ($row as $r) {
        echo "<td>" . htmlspecialchars(_crypt($r, 'd'), 3)  . "</td>";
    }
    echo "</tr>";
}
echo "<tbody></table></div><a href='user.php'>Back</a> ";
include("includes/js.php");
echo "</body></html>";
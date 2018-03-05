<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 05/03/2018
 * Time: 13:34
 */

// Check user is authenticated
if(!$_SESSION['username']) {
    header("location: ../logout.php");
}

echo "<h4>Activity Log</h4>";

$log = csv_to_array('test.csv');


echo "<table class='table table-sm table-responsive'>";
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
echo "<tbody>";
echo "</table>";
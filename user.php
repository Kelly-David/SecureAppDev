<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 19/02/2018
 * Time: 13:46
 */
require_once ("db/dbconfig.php");

session_start();

$email = $dob = $username = "";

// Check user is authenticated
if(!$_SESSION['username']) {
    header("location: logout.php");
}
else {
    // Escape user params
    $email = htmlspecialchars($_SESSION['username'], 3);

    $sql = "SELECT username, dob FROM `user` WHERE email = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;
        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_store_result($stmt);

            if ((mysqli_stmt_num_rows($stmt) == 1)) {

                mysqli_stmt_bind_result($stmt, $r_username, $r_dob);

                if (mysqli_stmt_fetch($stmt)) {
                    $username = htmlspecialchars($r_username, 3);
                    $dob = htmlspecialchars($r_dob, 3);
                }
            }
        }
    }
}

echo "
<!DOCTYPE html>
<html>
<head>";
include("includes/styles.php");
echo "
    <title>Profile</title>
    </head>
    <body>
    <div class='container-fluid'>
    <div class='row' style='margin: 1rem 0 0 0'>
    <div class='col-lg-3'>
    <p></p>
    </div>
    <div class='col-lg-6 col-sm-12'>
    <div class='card'>
    <div class='card-header'>
    Profile
    </div>
    <div class='card-body'>
    <p>";
echo "Username: ". $username;
echo "<br>";
echo "Email: " . $email;
echo "<br>";
echo "Date of Birth: " . $dob;
echo "</p>
      <form action='logout.php' method='post'>
      <br>
      <button type='submit' id='submit' class='btn btn-primary btn-sm float-right'><i class='fa fa-sign-in' aria-hidden='true'></i> Logout</button>
      </form>
      </div>
      <div class='card-footer'>
      </div>
      </div>
      </div>
      </div>";
include("includes/js.php");
echo "
    </div>
    </body>
    </html>";



?>
<html>
<head></head>
<body>
<p><?php echo $_SESSION['username']; ?></p>
<form action="logout.php">
    <button>
        Logout
    </button>
</form>

<form action="forgotpassword.php">
    <button>
        Logout
    </button>
</form>
</body>
</html>



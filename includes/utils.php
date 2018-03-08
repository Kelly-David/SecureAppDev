<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 16/02/2018
 * Time: 14:14
 */

date_default_timezone_set("Europe/Dublin");

/**
 * @param $data
 */
function debug_to_console($data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}

/**
 * @return DateTime|string
 */
function getTime() {
    date_default_timezone_set("Europe/Dublin");
    $server_time = new DateTime();
    $server_time = $server_time->format('Y-m-d H:i:s');
    return $server_time;
}

/**
 * returns true if email is in the db
 * @param $email
 * @param $link
 * @return bool
 */
function emailRegistered($email, $link) {
    $valid = false;
    $sql = "SELECT email FROM `user` WHERE email = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = _crypt($email);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // Email exists in the db
                $valid = true;
            }
        } else {
            logger("QUERY ERROR", $email, "utils.php", "EXCEPTION", $sql);
        }
    }
    return $valid;
}

/**
 * Returns username of associated email
 * @param $email
 * @param $link
 * @return bool|string
 */
function getUser($email, $link) {
    $username = "";
    $sql = "SELECT username FROM `user` WHERE email = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = _crypt($email);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $r_username);
                if (mysqli_stmt_fetch($stmt)) {
                    $username = _crypt($r_username, 'd');
                }
            }
        } else {
            logger("QUERY ERROR", $email, "utils.php", "EXCEPTION", $sql);
        }
    }
    return $username;
}

/**
 * @param $email
 * @return bool
 */
function emailIsValid($email) {
    $valid= false;
    if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $valid = true;
    }
    return $valid;
}

/**
 * Password must satisfy 3 of the following rules https://technet.microsoft.com/en-us/library/cc786468(v=ws.10).aspx
 * Regex ref: https://stackoverflow.com/questions/3466850/regular-expression-to-enforce-complex-passwords-matching-3-out-of-4-rules
 * @param $pw
 * @return bool
 */
function passwordRegex($pw) {
    $valid = false;
    if(preg_match('/^(?:(?=.*[a-z])(?:(?=.*[A-Z])(?=.*[\d\W])|(?=.*\W)(?=.*\d))|(?=.*\W)(?=.*[A-Z])(?=.*\d)).{8,}$/', $pw)) {
        $valid = true;
    }
    return $valid;
}

/**
 * @param $param
 * @param $case
 * @param string $link
 * @param string $email
 * @param string $user
 * @return bool
 * @internal param $valid bool
 */
function validate($param, $case, $link = "", $email = "", $user = "") {
    $valid = false;
    switch ($case) {
        case "email":
            if(emailRegistered($param, $link) && emailIsValid($param)) { $valid = true; }
            break;
        case "password":
            if(passwordComplexity($param, $email, $user )) { $valid = true; }
            break;
        case "user":
            if($param != "") { $valid = true; }
            break;
        case "dob":
            if($param != "") { $valid = true; }
            break;
        default:
            break;
    }
    return $valid;
}

/**
 * Log format : TIMESTAMP, ACTION, USER REF, SOURCE, RESULT
 * @param $event - the intended action being performed
 * @param $user - the user performing the action
 * @param $source - the page from which the action originated
 * @param $result - the outcome of the action (SUCCESS, DENY, EXCEPTION).
 * @internal param $ $
 */
function logger($event, $user, $source, $result, $payload = "" ) {
    $file = fopen('test.csv', 'a+') or die("Can't open file.");
    $now = getTime();
    $txt = _crypt($now) . ",[". _crypt($event) ."]," . _crypt($user) .  "," . _crypt($source) . "," . _crypt($result) . "," . _crypt($payload) . "\n";
    fwrite($file, $txt);
    fclose($file);
}

/**
 * Encrypt and decrypt: PHP encrypt and decrypt using OpenSSL
 * This function is used to encrypt data before writing to the database/log and decrypt upon retrieval from the database/log.
 * Adapted from https://gist.github.com/joashp/a1ae9cb30fa533f4ad94
 * @link http://php.net/manual/en/function.openssl-encrypt.php
 * @param string $string string to be encrypted/decrypted
 * @param string $action what to do with this? e for encrypt, d for decrypt
 * @return bool
 */
function _crypt( $string, $action = 'e') {

    $output = false;

    // Encryption method
    $encrypt_method = "AES-256-CBC";

    // Secrets
    $secret_key = 'secure_app_secret_key';
    $secret_iv = 'secure_app_secret_iv';

    // Hash
    $key = hash('sha256', $secret_key);

    // Initialization Vector (AES uses 16 byte blocks)
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

    if( $action == 'e' ) {
        // Encrypts given data with given method and key, returns a raw or base64 encoded string
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
        // Takes a raw or base64 encoded string and decrypts it using a given method and key.
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }
    return $output;
}

/**
 * password_hash: randomly generates a salted hash for each password
 * @param $password
 * @return string
 */
function _hash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * @param $client
 * @param $link
 * @param $param
 * @return string
 */
function clientAttemptQuery($client, $link, $param = "any") {
    $session_sql = "UPDATE clientSession SET Counter = Counter + 1, Tstamp = NOW() WHERE SessionID = '$client'";
    $result = mysqli_query($link,$session_sql);
    if (!$result) {
        logger("QUERY ERROR", $client, "utils.php", "EXCEPTION", $session_sql );
        die('SQL error. Could not query');
    }
    return "Email "  . htmlspecialchars($param, 3) . " invalid. Login or re-enter.";

}

/**
 * @param $client
 * @param $link
 * @return string
 * @internal param $param
 */
function clientAttemptReset($client, $link) {
    $session_sql = "UPDATE clientSession SET Counter = 0, Tstamp = NOW() WHERE SessionID = '$client'";
    $result = mysqli_query($link,$session_sql);
    if (!$result) {
        logger("QUERY ERROR", $client, "utils.php", "EXCEPTION");
        die('SQL error. Could not query');
    }
}


/**
 * Password complexity rules according to https://technet.microsoft.com/en-us/library/cc786468(v=ws.10).aspx
 * @param $password
 * @param $username
 * @param $email
 * @return bool
 * @internal param $ $
 */
function passwordComplexity($password, $username, $email) {
    $valid = true;
    // If the password contains the entire email
    if (strpos($password, $email) !== false) {
        $valid =  false;
    }
    // Split the username by delimiters [not alpha numeric characters]
    $username_keywords = preg_split("/[^a-zA-Z0-9]+/", $username);
    foreach ($username_keywords as $key) {
        // Tokens greater than or equal to 3 characters
        if (strlen($key) > 2) {

            // Check the token is not a substring of the password
            if (strpos($password, $key) !== false ) {
                $valid = false;
            }
        }
    }
    // Does the password match the regex
    if(!passwordRegex($password)) {
        debug_to_console("False regex: " . $valid);
        $valid = false;
    }
    return $valid;
}

/**
 * @link http://gist.github.com/385876
 * @param string $filename
 * @param string $delimiter
 * @return array|bool
 */
function csv_to_array($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}
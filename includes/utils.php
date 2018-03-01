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
    // Time
    $server_time = new DateTime();
    $server_time = $server_time->format('Y-m-d H:i:s');
    return $server_time;
}

/**
 * @param $email
 * @param $link
 * @return bool
 */
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

function passwordRegex($pw) {
    $valid = false;
    if(preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/', $pw)) {
        $valid = true;
    }
    return $valid;
}

/**
 * @param $param
 * @param $case
 * @param $link
 * @return bool
 */
function validate($param, $case, $link = "") {
    $valid = false;
    switch ($case) {
        case "email":
            if(emailRegistered($param, $link) && emailIsValid($param)) { $valid = true; }
            break;
        case "password":
            if(passwordRegex($param)) { $valid = true; }
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

function logger($event, $user ) {
    $file = fopen('test.txt', 'a+') or die("Can't open file.");
    $now = getTime();
    $user = _crypt($user, 'e');
    $txt = $now . " [". $event ."] " . "Ref: " . $user . "\n";
    fwrite($file, $txt);
    fclose($file);
}

/**
 * Encrypt and decrypt
 * @author Nazmul Ahsan <n.mukto@gmail.com>
 * @link http://nazmulahsan.me/simple-two-way-function-encrypt-decrypt-string/
 * @param string $string string to be encrypted/decrypted
 * @param string $action what to do with this? e for encrypt, d for decrypt
 * @return bool
 */
function _crypt( $string, $action = 'e' ): bool {

    $secret_key = 'my_simple_secret_key';
    $secret_iv = 'my_simple_secret_iv';

    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

    if( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }

    return $output;
}

/**
 * @param $password
 * @return string
 */
function _hash($password): string {
    return password_hash($password, PASSWORD_DEFAULT);
}
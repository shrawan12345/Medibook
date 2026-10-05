<?php


session_start();

include "db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/PHPMailer/Exception.php";
require __DIR__ . "/PHPMailer/PHPMailer.php";
require __DIR__ . "/PHPMailer/SMTP.php";


/* Check if form was submitted */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: Pages/Login.html");
    exit();

}


/* Get form data */

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';


/* Validate */

if ($email === '' || $password === '') {

    die("Please enter your email and password.");

}


/* Find user */

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");

mysqli_stmt_bind_param($stmt, "s", $email);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


if (!$result) {

    die("Database error: " . mysqli_error($conn));

}


/* Check account */

if (mysqli_num_rows($result) !== 1) {

    die("Account not found.");

}


$user = mysqli_fetch_assoc($result);


/* Verify password */

if (!password_verify($password, $user['password'])) {

    die("Incorrect password.");

}


/*
    Password is correct.
    Now generate a 6-digit OTP.
*/

$otp = random_int(100000, 999999);


/*
    Store a HASH of the OTP in the database.
    OTP expires after 5 minutes.
*/

$otp_hash = password_hash($otp, PASSWORD_DEFAULT);

$otp_expires = date("Y-m-d H:i:s", time() + (5 * 60));

$otp_attempts = 0;


/* Update OTP information */

$update = mysqli_prepare(
    $conn,
    "UPDATE users
     SET otp_code = ?, otp_expires = ?, otp_attempts = ?
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $update,
    "ssii",
    $otp_hash,
    $otp_expires,
    $otp_attempts,
    $user['id']
);

if (!mysqli_stmt_execute($update)) {

    die("Could not create OTP: " . mysqli_error($conn));

}


/*
    Send OTP by email using PHPMailer.
*/

$mail_config = require __DIR__ . "/mail_config.php";

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();

    $mail->Host = $mail_config['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $mail_config['username'];
    $mail->Password = $mail_config['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $mail_config['port'];

    $mail->setFrom(
        $mail_config['username'],
        $mail_config['from_name']
    );

    $mail->addAddress($user['email'], $user['name']);

    $mail->isHTML(true);

    $mail->Subject = "MediBook Login Verification Code";

    $mail->Body = "
        <h2>MediBook Login Verification</h2>

        <p>Hello {$user['name']},</p>

        <p>Your MediBook verification code is:</p>

        <h1>$otp</h1>

        <p>This code will expire in <strong>5 minutes</strong>.</p>

        <p>If you did not try to log in, you can ignore this email.</p>
    ";

    $mail->AltBody =
        "Your MediBook verification code is: $otp. "
        . "This code will expire in 5 minutes.";

    $mail->send();

} catch (Exception $e) {

    die("Could not send OTP email. Please try again later.");

}


/*
    Store only the user ID temporarily.
    The actual login session is NOT created yet.
*/

$_SESSION['otp_user_id'] = $user['id'];


/*
    Go to OTP verification page.
*/

header("Location: verify_otp.php");

exit();

?>
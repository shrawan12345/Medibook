<?php

session_start();

include "db.php";


/* Check whether an OTP login is in progress */

if (!isset($_SESSION['otp_user_id'])) {

    header("Location: Pages/Login.html");
    exit();

}


$user_id = $_SESSION['otp_user_id'];


/* Check whether OTP was submitted */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    ?>

    <!DOCTYPE html>
    <html>
    <head>

        <title>Verify OTP</title>

        <style>

            body {
                font-family: Arial, sans-serif;
                background: #f4f6f8;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            .container {
                background: white;
                padding: 30px;
                width: 350px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                text-align: center;
            }

            input {
                width: 90%;
                padding: 12px;
                margin: 10px 0;
                font-size: 18px;
                text-align: center;
                letter-spacing: 5px;
            }

            button {
                width: 100%;
                padding: 12px;
                background: #007bff;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

        </style>

    </head>

    <body>

        <div class="container">

            <h2>Verify Your Email</h2>

            <p>We sent a 6-digit verification code to your email.</p>

            <form method="POST">

                <input
                    type="text"
                    name="otp"
                    maxlength="6"
                    minlength="6"
                    pattern="[0-9]{6}"
                    placeholder="Enter OTP"
                    required
                >

                <button type="submit">
                    Verify OTP
                </button>

            </form>

        </div>

    </body>
    </html>

    <?php

    exit();

}


/* Get OTP */

$otp = trim($_POST['otp'] ?? '');


/* Validate OTP format */

if (!preg_match('/^[0-9]{6}$/', $otp)) {

    die("Please enter a valid 6-digit OTP.");

}


/* Get user */

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM users WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


if (mysqli_num_rows($result) !== 1) {

    die("User not found.");

}


$user = mysqli_fetch_assoc($result);


/*
    Check number of failed attempts.
*/

if ($user['otp_attempts'] >= 5) {

    die("Too many incorrect OTP attempts. Please login again.");

}


/*
    Check whether OTP has expired.
*/

if (
    empty($user['otp_expires']) ||
    strtotime($user['otp_expires']) < time()
) {

    die("OTP has expired. Please login again.");

}


/*
    Verify OTP.
*/

if (!password_verify($otp, $user['otp_code'])) {

    $new_attempts = $user['otp_attempts'] + 1;

    $update = mysqli_prepare(
        $conn,
        "UPDATE users SET otp_attempts = ? WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $update,
        "ii",
        $new_attempts,
        $user_id
    );

    mysqli_stmt_execute($update);

    die("Incorrect OTP.");

}


/*
    OTP is correct.
    Now create the real login session.
*/

$_SESSION['user_id'] = $user['id'];

$_SESSION['user_name'] = $user['name'];

$_SESSION['user_email'] = $user['email'];

$_SESSION['user_role'] = $user['role'];


/*
    Clear OTP information.
*/

$clear = mysqli_prepare(
    $conn,
    "UPDATE users
     SET otp_code = NULL,
         otp_expires = NULL,
         otp_attempts = 0
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $clear,
    "i",
    $user_id
);

mysqli_stmt_execute($clear);


/*
    Remove temporary OTP session.
*/

unset($_SESSION['otp_user_id']);


/*
    Redirect according to role.
*/

if ($user['role'] === 'admin') {

    header("Location: admin.php");
    exit();

}


header("Location: dashboard.php");

exit();

?>
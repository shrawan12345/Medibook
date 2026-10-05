
<?php

session_start();

include "db.php";


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

$email = mysqli_real_escape_string($conn, $email);

$sql = "SELECT * FROM users WHERE email = '$email'";

$result = mysqli_query($conn, $sql);


if (!$result) {

    die("Database error: " . mysqli_error($conn));

}


/* Check account */

if (mysqli_num_rows($result) === 1) {

    $user = mysqli_fetch_assoc($result);


    /* Verify password */

    if (password_verify($password, $user['password'])) {


        /* Create session */

        $_SESSION['user_id'] = $user['id'];

        $_SESSION['user_name'] = $user['name'];

        $_SESSION['user_email'] = $user['email'];

        $_SESSION['user_role'] = $user['role'];


        /* Admin goes to Admin Dashboard */

        if ($user['role'] === 'admin') {

            header("Location: admin.php");
            exit();

        }


        /* Normal user goes to User Dashboard */

        header("Location: dashboard.php");
        exit();


    } else {

        die("Incorrect password.");

    }


} else {

    die("Account not found.");

}

?>


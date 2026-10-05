
<?php

include "db.php";


/* Check if form was submitted */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: Pages/Register.html");
    exit();

}


/* Get form data */

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';


/* Validate fields */

if ($name === '' || $email === '' || $password === '' || $confirmPassword === '') {

    die("Please fill in all fields.");

}


/* Check password */

if ($password !== $confirmPassword) {

    die("Passwords do not match!");

}


/* Check if email already exists */

$safe_email = mysqli_real_escape_string($conn, $email);

$check_sql = "SELECT id FROM users WHERE email = '$safe_email'";

$check_result = mysqli_query($conn, $check_sql);


if (!$check_result) {

    die("Database error: " . mysqli_error($conn));

}


if (mysqli_num_rows($check_result) > 0) {

    die("An account with this email already exists.");

}


/* Secure password */

$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);


/* Escape name */

$safe_name = mysqli_real_escape_string($conn, $name);


/* Register user */

$sql = "INSERT INTO users
        (name, email, password, role)
        VALUES
        ('$safe_name', '$safe_email', '$hashedPassword', 'user')";


if (mysqli_query($conn, $sql)) {

    echo "<h2>Registration successful!</h2>";

    echo "<p>Your account has been created successfully.</p>";

    echo "<p>
            <a href='Pages/Login.html'>
                Go to Login
            </a>
          </p>";

} else {

    echo "Error: " . mysqli_error($conn);

}

?>
```

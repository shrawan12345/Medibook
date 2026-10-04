```php
<?php

session_start();

include "db.php";


/* Check login */

if (!isset($_SESSION['user_id'])) {
    header("Location: Pages/Login.html");
    exit();
}

$user_id = $_SESSION['user_id'];


/* Check admin role */

$sql = "SELECT role FROM users WHERE id = '$user_id'";

$result = mysqli_query($conn, $sql);

$user = mysqli_fetch_assoc($result);

if (!$user || $user['role'] !== 'admin') {
    die("Access denied. Admins only.");
}


/* Add doctor */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $specialization = trim($_POST['specialization']);


    /* Validate fields */

    if ($name === "" || $specialization === "") {
        die("Please fill in all fields.");
    }


    /* Escape input */

    $name = mysqli_real_escape_string($conn, $name);

    $specialization = mysqli_real_escape_string(
        $conn,
        $specialization
    );


    /* Insert doctor */

    $sql = "INSERT INTO doctors
            (name, specialization)
            VALUES
            ('$name', '$specialization')";


    if (mysqli_query($conn, $sql)) {

        header("Location: admin_doctors.php");
        exit();

    } else {

        echo "Error: " . mysqli_error($conn);

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>MediBook - Add Doctor</title>

    <link rel="stylesheet" href="Css/Style.css">

</head>

<body>

<header>

    <nav class="navbar">

        <h1 class="logo">
            MediBook Admin
        </h1>

        <ul class="nav-links">

            <li>
                <a href="admin.php">
                    Appointments
                </a>
            </li>

            <li>
                <a href="admin_users.php">
                    Users
                </a>
            </li>

            <li>
                <a href="admin_doctors.php">
                    Doctors
                </a>
            </li>

            <li>
                <a href="logout.php">
                    Logout
                </a>
            </li>

        </ul>

    </nav>

</header>


<main>

<section class="services">

    <h2>
        Add New Doctor
    </h2>


    <form method="POST">

        <label for="name">
            Doctor Name
        </label>

        <input
            type="text"
            id="name"
            name="name"
            placeholder="Enter doctor name"
            required
        >


        <label for="specialization">
            Specialization
        </label>

        <input
            type="text"
            id="specialization"
            name="specialization"
            placeholder="Enter specialization"
            required
        >


        <button type="submit"
                class="book-btn">

            Add Doctor

        </button>

    </form>

</section>

</main>


<footer>

    <p>
        © 2026 MediBook. All Rights Reserved.
    </p>

</footer>

</body>

</html>
```

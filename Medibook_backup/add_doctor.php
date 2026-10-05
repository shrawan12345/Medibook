
Add doctor · PHP
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
    $nmc_number = trim($_POST['nmc_number']);
 
 
    /* Validate fields */
 
    if ($name === "" || $specialization === "" || $nmc_number === "") {
        die("Please fill in all fields.");
    }
 
    if (!preg_match('/^\d{3,10}$/', $nmc_number)) {
        die("NMC number must be 3 to 10 digits.");
    }
 
 
    /* Escape input */
 
    $name = mysqli_real_escape_string($conn, $name);
 
    $specialization = mysqli_real_escape_string(
        $conn,
        $specialization
    );
 
    $nmc_number = mysqli_real_escape_string($conn, $nmc_number);
 
 
    /* Insert doctor */
 
    $sql = "INSERT INTO doctors
            (name, specialization, nmc_number)
            VALUES
            ('$name', '$specialization', '$nmc_number')";
 
 
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
 
 
        <label for="nmc_number">
            NMC Number
        </label>
 
        <input
            type="text"
            id="nmc_number"
            name="nmc_number"
            placeholder="Enter NMC registration number"
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
 

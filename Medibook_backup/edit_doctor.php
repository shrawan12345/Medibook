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

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result);

if (!$user || $user['role'] !== 'admin') {
    die("Access denied. Admins only.");
}


/* Check doctor ID */

if (!isset($_GET['id'])) {
    die("Doctor ID is missing.");
}

$id = $_GET['id'];


/* Get doctor */

$sql = "SELECT * FROM doctors WHERE id = '$id'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) != 1) {
    die("Doctor not found.");
}

$doctor = mysqli_fetch_assoc($result);


/* Update doctor */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $specialization = trim($_POST['specialization']);


    /* Validate */

    if ($name === "" || $specialization === "") {
        die("Please fill in all fields.");
    }


    /* Escape input */

    $name = mysqli_real_escape_string(
        $conn,
        $name
    );

    $specialization = mysqli_real_escape_string(
        $conn,
        $specialization
    );


    /* Update */

    $sql = "UPDATE doctors
            SET name = '$name',
                specialization = '$specialization'
            WHERE id = '$id'";


    if (mysqli_query($conn, $sql)) {

        header("Location: admin_doctors.php");
        exit();

    } else {

        die(
            "Update error: "
            . mysqli_error($conn)
        );

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Doctor - MediBook</title>

    <link rel="stylesheet" href="Css/Style.css">

</head>

<body>

<header>

    <nav class="navbar">

        <h1 class="logo">
            MediBook Admin
        </h1>

    </nav>

</header>


<main>

<section class="services">

    <h2>
        Edit Doctor
    </h2>


    <form method="POST">

        <label for="name">
            Doctor Name
        </label>

        <input
            type="text"
            id="name"
            name="name"
            value="<?php echo htmlspecialchars($doctor['name']); ?>"
            required
        >


        <label for="specialization">
            Specialization
        </label>

        <input
            type="text"
            id="specialization"
            name="specialization"
            value="<?php echo htmlspecialchars($doctor['specialization']); ?>"
            required
        >


        <button type="submit"
                class="book-btn">

            Update Doctor

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

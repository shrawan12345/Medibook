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

$sql = "SELECT role
        FROM users
        WHERE id = '$user_id'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result);

if (!$user || $user['role'] !== 'admin') {
    die("Access denied. Admins only.");
}


/* Get users */

$sql = "SELECT id, name, email, role
        FROM users
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>MediBook - Users</title>

    <link rel="stylesheet"
          href="Css/Style.css">

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
                <a href="admin_doctors.php">
                    Doctors
                </a>
            </li>

            <li>
                <a href="dashboard.php">
                    Dashboard
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
        Registered Users
    </h2>


    <div class="appointment-table-container">

        <table class="appointment-table">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Name</th>

                    <th>Email</th>

                    <th>Role</th>

                </tr>

            </thead>


            <tbody>

            <?php if (mysqli_num_rows($result) > 0) { ?>

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['id']
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['name']
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['email']
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['role']
                            );
                            ?>
                        </td>

                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>

                    <td colspan="4">
                        No registered users found.
                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</section>

</main>


<footer>

    <p>
        © 2026 MediBook. All Rights Reserved.
    </p>

</footer>


</body>

</html>

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


/* Get doctors */

$sql = "SELECT * FROM doctors ORDER BY id DESC";

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

    <title>MediBook - Manage Doctors</title>

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
        Manage Doctors
    </h2>


    <br>


    <!-- Add Doctor -->

    <a href="add_doctor.php"
       class="book-btn">

        Add Doctor

    </a>


    <br><br>


    <!-- Doctors Table -->

    <div class="appointment-table-container">

        <table class="appointment-table">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Doctor Name</th>

                    <th>Specialization</th>

                    <th>NMC Number</th>

                    <th>Action</th>

                </tr>

            </thead>


            <tbody>

            <?php if (mysqli_num_rows($result) > 0) { ?>

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <tr>

                        <td>
                            <?php
                            echo $row['id'];
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
                                $row['specialization']
                            );
                            ?>
                        </td>

                        <td>
    <?php
    echo htmlspecialchars($row['nmc_number']);
    ?>
</td>

                        


                        <td>

                            <a href="edit_doctor.php?id=<?php echo $row['id']; ?>">
                                Edit
                            </a>

                            <br><br>

                            <a href="delete_doctor.php?id=<?php echo $row['id']; ?>"
                               onclick="return confirm('Are you sure you want to delete this doctor?');">

                                Delete

                            </a>

                        </td>

                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>

                    <td colspan="5">
                        No doctors found.
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


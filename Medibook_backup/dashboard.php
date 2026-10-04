<?php

session_start();

include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: Pages/Login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Appointment Statistics */

$total_appointments = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM appointsment
         WHERE user_id = '$user_id'"
    )
)['total'];

$pending_appointments = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM appointsment
         WHERE user_id = '$user_id'
         AND status = 'Pending'"
    )
)['total'];

$confirmed_appointments = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM appointsment
         WHERE user_id = '$user_id'
         AND status = 'Confirmed'"
    )
)['total'];

$cancelled_appointments = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM appointsment
         WHERE user_id = '$user_id'
         AND status = 'Cancelled'"
    )
)['total'];

/* Check user role */

$user_role = '';

$sql_role = "SELECT role FROM users WHERE id = '$user_id'";
$result_role = mysqli_query($conn, $sql_role);

if ($result_role) {

    $role_data = mysqli_fetch_assoc($result_role);

    if ($role_data) {
        $user_role = $role_data['role'];
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>MediBook - Dashboard</title>

    <link rel="stylesheet" href="Css/Style.css">

</head>

<body>

<header>

    <nav class="navbar">

        <h1 class="logo">MediBook</h1>

        <ul class="nav-links">

            <li>
                <a href="Index.html">Home</a>
            </li>

            <li>
                <a href="Pages/Doctors.php">Doctors</a>
            </li>

            <li>
                <a href="Pages/Appointment.html">Appointments</a>
            </li>

            <li>
                <a href="my_appointment.php">My Appointments</a>
            </li>

            <?php if ($user_role === 'admin') { ?>

                <li>
                    <a href="admin.php">Admin Dashboard</a>
                </li>

            <?php } ?>

        </ul>

    </nav>

</header>

<main>

<section class="services">

    <h2>Welcome to MediBook</h2>

    <!-- Appointment Statistics -->

    <div class="admin-stats">

        <div class="stat-card">
            <h3>Total Appointments</h3>
            <p>
                <?php echo $total_appointments; ?>
            </p>
        </div>

        <div class="stat-card">
            <h3>Pending</h3>
            <p>
                <?php echo $pending_appointments; ?>
            </p>
        </div>

        <div class="stat-card">
            <h3>Confirmed</h3>
            <p>
                <?php echo $confirmed_appointments; ?>
            </p>
        </div>

        <div class="stat-card">
            <h3>Cancelled</h3>
            <p>
                <?php echo $cancelled_appointments; ?>
            </p>
        </div>

    </div>

    <br>

    <p>
        Hello,
        <strong>
            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </strong>!
    </p>

    <p>
        Email:
        <?php echo htmlspecialchars($_SESSION['user_email']); ?>
    </p>

    <br>

    <a href="Pages/Appointment.html"
       class="book-btn">
        Book an Appointment
    </a>

    <br><br>

    <a href="my_appointment.php"
       class="book-btn">
        My Appointments
    </a>

    <br><br>

    <a href="logout.php">
        Logout
    </a>

</section>

</main>

<footer>

    <p>© 2026 MediBook. All Rights Reserved.</p>

</footer>

</body>

</html>
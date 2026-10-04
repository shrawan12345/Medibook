<?php

session_start();

include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: Pages/Login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM appointsment
        WHERE user_id = '$user_id'
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

    <title>MediBook - My Appointments</title>

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
                <a href="dashboard.php">Dashboard</a>
            </li>

            <li>
                <a href="logout.php">Logout</a>
            </li>

        </ul>

    </nav>

</header>

<main>

<section class="services">

    <h2>My Appointments</h2>

    <div class="appointment-table-container">

        <table class="appointment-table">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Doctor</th>
                    <th>Phone</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

            </thead>

            <tbody>

            <?php if (mysqli_num_rows($result) > 0) { ?>

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                    <tr>

                        <td>
                            <?php echo $row['id']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['doctor']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['phone']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['appointment_date']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['appointment_time']); ?>
                        </td>

                        <td>

                            <span class="status-badge status-<?php echo strtolower($row['status']); ?>">

                                <?php echo htmlspecialchars($row['status']); ?>

                            </span>

                        </td>

                        <td>

                            <?php if ($row['status'] === 'Pending') { ?>

                                <a href="edit_appointment.php?id=<?php echo $row['id']; ?>">
                                    Edit
                                </a>

                                <br><br>

                                <a class="cancel-btn"
                                   href="cancel_appointment.php?id=<?php echo $row['id']; ?>"
                                   onclick="return confirm('Are you sure you want to cancel this appointment?');">

                                    Cancel

                                </a>

                            <?php } elseif ($row['status'] === 'Confirmed') { ?>

                                <a class="cancel-btn"
                                   href="cancel_appointment.php?id=<?php echo $row['id']; ?>"
                                   onclick="return confirm('Are you sure you want to cancel this appointment?');">

                                    Cancel

                                </a>

                            <?php } else { ?>

                                <span>
                                    Cancelled
                                </span>

                            <?php } ?>

                        </td>

                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>

                    <td colspan="7">
                        No appointments found.
                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</section>

</main>

<footer>

    <p>© 2026 MediBook. All Rights Reserved.</p>

</footer>

</body>

</html>
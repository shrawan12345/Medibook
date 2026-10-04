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


/* Statistics */

$total_users = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM users"
    )
)['total'];


$total_appointments = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM appointsment"
    )
)['total'];


$pending_appointments = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM appointsment
         WHERE status = 'Pending'"
    )
)['total'];


$confirmed_appointments = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM appointsment
         WHERE status = 'Confirmed'"
    )
)['total'];


$cancelled_appointments = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM appointsment
         WHERE status = 'Cancelled'"
    )
)['total'];


/* Search and filter */

$search = "";
$status_filter = "";


if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}


if (isset($_GET['status'])) {
    $status_filter = $_GET['status'];
}


/* Build appointment query */

$sql = "SELECT * FROM appointsment WHERE 1=1";


if ($search !== "") {

    $safe_search = mysqli_real_escape_string($conn, $search);

    $sql .= " AND (
                name LIKE '%$safe_search%'
                OR email LIKE '%$safe_search%'
                OR phone LIKE '%$safe_search%'
                OR doctor LIKE '%$safe_search%'
              )";
}


if (
    $status_filter === 'Pending' ||
    $status_filter === 'Confirmed' ||
    $status_filter === 'Cancelled'
) {

    $safe_status = mysqli_real_escape_string(
        $conn,
        $status_filter
    );

    $sql .= " AND status = '$safe_status'";
}


$sql .= " ORDER BY id DESC";


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

    <title>MediBook - Admin Dashboard</title>

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
                <a href="dashboard.php">
                    Dashboard
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
        Admin Dashboard
    </h2>


    <!-- Statistics -->

    <div class="admin-stats">


        <div class="stat-card">

            <h3>
                Total Users
            </h3>

            <p>
                <?php echo $total_users; ?>
            </p>

        </div>


        <div class="stat-card">

            <h3>
                Total Appointments
            </h3>

            <p>
                <?php echo $total_appointments; ?>
            </p>

        </div>


        <div class="stat-card">

            <h3>
                Pending
            </h3>

            <p>
                <?php echo $pending_appointments; ?>
            </p>

        </div>


        <div class="stat-card">

            <h3>
                Confirmed
            </h3>

            <p>
                <?php echo $confirmed_appointments; ?>
            </p>

        </div>


        <div class="stat-card">

            <h3>
                Cancelled
            </h3>

            <p>
                <?php echo $cancelled_appointments; ?>
            </p>

        </div>


    </div>


    <br>


    <h2>
        Appointment Management
    </h2>


    <!-- Search and Filter -->

    <form method="GET"
          action="admin.php"
          style="margin-bottom: 20px;">


        <input
            type="text"
            name="search"
            placeholder="Search patient, email, phone or doctor"
            value="<?php echo htmlspecialchars($search); ?>"
        >


        <select name="status">

            <option value="">
                All Status
            </option>


            <option value="Pending"
                <?php
                if ($status_filter === 'Pending') {
                    echo 'selected';
                }
                ?>>
                Pending
            </option>


            <option value="Confirmed"
                <?php
                if ($status_filter === 'Confirmed') {
                    echo 'selected';
                }
                ?>>
                Confirmed
            </option>


            <option value="Cancelled"
                <?php
                if ($status_filter === 'Cancelled') {
                    echo 'selected';
                }
                ?>>
                Cancelled
            </option>

        </select>


        <button type="submit"
                class="book-btn">

            Search

        </button>


        <a href="admin.php">
            Clear
        </a>


    </form>


    <!-- Appointment Table -->

    <div class="appointment-table-container">


        <table class="appointment-table">


            <thead>

                <tr>

                    <th>ID</th>

                    <th>Patient</th>

                    <th>Email</th>

                    <th>Phone</th>

                    <th>Doctor</th>

                    <th>Date</th>

                    <th>Time</th>

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
                                $row['phone']
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['doctor']
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['appointment_date']
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $row['appointment_time']
                            );
                            ?>
                        </td>


                        <td>

                            <span class="status-badge status-<?php echo strtolower($row['status']); ?>">

                                <?php
                                echo htmlspecialchars(
                                    $row['status']
                                );
                                ?>

                            </span>

                        </td>


                        <!-- Action -->

                        <td>


                            <?php if ($row['status'] !== 'Cancelled') { ?>


                                <!-- Edit -->

                                <a href="admin_edit_appointment.php?id=<?php echo $row['id']; ?>">

                                    Edit

                                </a>


                                <br><br>


                                <?php if ($row['status'] === 'Pending') { ?>


                                    <!-- Confirm -->

                                    <a href="update_status.php?id=<?php echo $row['id']; ?>&status=Confirmed">

                                        Confirm

                                    </a>


                                    <br><br>


                                <?php } ?>


                                <!-- Cancel -->

                                <a
                                    href="update_status.php?id=<?php echo $row['id']; ?>&status=Cancelled"
                                    onclick="return confirm('Are you sure you want to cancel this appointment?');"
                                >

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

                    <td colspan="9">

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

    <p>
        © 2026 MediBook. All Rights Reserved.
    </p>

</footer>


</body>

</html>
```

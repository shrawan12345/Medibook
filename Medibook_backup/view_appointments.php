<?php
include 'db.php';

$sql = "SELECT * FROM appointsment ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediBook - Booked Appointments</title>

    <link rel="stylesheet" href="Css/Style.css">
</head>

<body>

<header>
    <nav class="navbar">
        <h1 class="logo">MediBook</h1>

        <ul class="nav-links">
            <li><a href="Index.html">Home</a></li>
            <li><a href="Pages/Doctors.html">Doctors</a></li>
            <li><a href="Pages/Appointment.html">Appointments</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="services">

        <h2>Booked Appointments</h2>

       <div class="appointment-table-container">

    <table class="appointment-table">

        <thead>
            <tr>
                <th>ID</th>
                <th>Patient Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Doctor</th>
                <th>Appointment Date</th>
            </tr>
        </thead>

        <tbody>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo $row['doctor']; ?></td>
                <td><?php echo $row['appointment_date']; ?></td>
            </tr>

        <?php } ?>

        </tbody>

    </table>

</div>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>

            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo $row['doctor']; ?></td>
                <td><?php echo $row['appointment_date']; ?></td>
            </tr>

            <?php } ?>

        </table>

    </section>
</main>

</body>
</html>
```php
<?php

include "../db.php";

$sql = "SELECT * FROM doctors ORDER BY id ASC";

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

    <title>MediBook - Doctors</title>

    <link rel="stylesheet" href="../Css/Style.css">

</head>

<body>

<header>

    <nav class="navbar">

        <h1 class="logo">
            MediBook
        </h1>

        <ul class="nav-links">

            <li>
                <a href="../Index.html">Home</a>
            </li>

            <li>
                <a href="Doctors.php">Doctors</a>
            </li>

            <li>
                <a href="Appointment.html">Appointments</a>
            </li>

            <li>
                <a href="../dashboard.php">Dashboard</a>
            </li>

            <li>
                <a href="../my_appointment.php">My Appointments</a>
            </li>

        </ul>

    </nav>

</header>


<main>

<section class="services">

    <h2>Our Doctors</h2>

    <div class="service-container">

        <?php if (mysqli_num_rows($result) > 0) { ?>

            <?php while ($doctor = mysqli_fetch_assoc($result)) { ?>

                <div class="service-card doctor-card">

                    <h3>
                        <?php
                        echo htmlspecialchars($doctor['name']);
                        ?>
                    </h3>

                    <p class="doctor-specialization">
                        <?php
                        echo htmlspecialchars($doctor['specialization']);
                        ?>
                    </p>

                    <a
                        href="Appointment.html?doctor=<?php echo urlencode($doctor['name']); ?>"
                        class="book-btn doctor-book-btn"
                    >
                        Book Appointment
                    </a>

                </div>

            <?php } ?>

        <?php } else { ?>

            <p>No doctors available at the moment.</p>

        <?php } ?>

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

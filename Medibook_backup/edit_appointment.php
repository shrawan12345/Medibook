```php
<?php

session_start();

include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: Pages/Login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Appointment ID is missing.");
}

$appointment_id = $_GET['id'];


/* Get only this user's appointment */

$sql = "SELECT * FROM appointsment
        WHERE id = '$appointment_id'
        AND user_id = '$user_id'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) != 1) {
    die("Appointment not found.");
}

$appointment = mysqli_fetch_assoc($result);


/* Prevent editing cancelled appointments */

if ($appointment['status'] === 'Cancelled') {
    die("This appointment has been cancelled and cannot be edited.");
}


/* Get doctors */

$sql_doctors = "SELECT * FROM doctors ORDER BY name";

$result_doctors = mysqli_query($conn, $sql_doctors);

if (!$result_doctors) {
    die("Database error: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Appointment - MediBook</title>

    <link rel="stylesheet" href="Css/Style.css">

</head>

<body>

<header>

    <nav class="navbar">

        <h1 class="logo">MediBook</h1>

    </nav>

</header>

<main>

<section class="services">

    <h2>Reschedule Appointment</h2>

    <form action="update_appointment.php" method="POST">

        <input type="hidden"
               name="id"
               value="<?php echo $appointment['id']; ?>">


        <!-- Doctor -->

        <label for="doctor">
            Doctor
        </label>

        <select name="doctor"
                id="doctor"
                required>

            <option value="">
                Choose a doctor
            </option>

            <?php while ($doctor = mysqli_fetch_assoc($result_doctors)) { ?>

                <option
                    value="<?php echo htmlspecialchars($doctor['name']); ?>"
                    <?php
                    if ($appointment['doctor'] == $doctor['name']) {
                        echo 'selected';
                    }
                    ?>
                >

                    <?php echo htmlspecialchars($doctor['name']); ?>
                    -
                    <?php echo htmlspecialchars($doctor['specialization']); ?>

                </option>

            <?php } ?>

        </select>


        <!-- Appointment Date -->

        <label for="appointment_date">
            Appointment Date
        </label>

        <input
            type="date"
            name="appointment_date"
            id="appointment_date"
            value="<?php echo htmlspecialchars($appointment['appointment_date']); ?>"
            required
        >


        <!-- Appointment Time -->

        <label for="appointment_time">
            Appointment Time
        </label>

        <input
            type="time"
            name="appointment_time"
            id="appointment_time"
            value="<?php echo htmlspecialchars($appointment['appointment_time']); ?>"
            required
        >


        <button type="submit"
                class="book-btn">

            Update Appointment

        </button>

    </form>

</section>

</main>


<script>

/* Prevent selecting a past date */

const dateInput =
    document.getElementById("appointment_date");

const today = new Date();

const year =
    today.getFullYear();

const month =
    String(today.getMonth() + 1).padStart(2, "0");

const day =
    String(today.getDate()).padStart(2, "0");

dateInput.min =
    `${year}-${month}-${day}`;

</script>

</body>

</html>
```

```php
<?php

session_start();

include "db.php";


/* Check login */

if (!isset($_SESSION['user_id'])) {
    die("Please login first.");
}

$user_id = $_SESSION['user_id'];


/* Check request method */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}


/* Check required fields */

if (
    empty($_POST['name']) ||
    empty($_POST['email']) ||
    empty($_POST['phone']) ||
    empty($_POST['doctor']) ||
    empty($_POST['appointment_date']) ||
    empty($_POST['appointment_time'])
) {
    die("Please fill in all required fields.");
}


/* Get form data */

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$doctor_id = $_POST['doctor'];
$appointment_date = $_POST['appointment_date'];
$appointment_time = $_POST['appointment_time'];


/* Validate email */

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email address.");
}


/* Validate doctor ID */

if (!is_numeric($doctor_id)) {
    die("Invalid doctor selected.");
}


/* Validate date */

$today = date("Y-m-d");

if ($appointment_date < $today) {
    die("Appointment date cannot be in the past.");
}


/* Validate time */

if (!preg_match('/^\d{2}:\d{2}$/', $appointment_time)) {
    die("Invalid appointment time.");
}

if ($appointment_time < '09:00' || $appointment_time > '17:00') {
    die("Appointment time must be between 9:00 AM and 5:00 PM.");
}


/* Get doctor */

$sql_doctor = "SELECT name
               FROM doctors
               WHERE id = '$doctor_id'";

$result_doctor = mysqli_query($conn, $sql_doctor);

if (!$result_doctor) {
    die("Database error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result_doctor) != 1) {
    die("Invalid doctor selected.");
}

$doctor_data = mysqli_fetch_assoc($result_doctor);

$doctor = $doctor_data['name'];


/* Escape data */

$name = mysqli_real_escape_string($conn, $name);
$email = mysqli_real_escape_string($conn, $email);
$phone = mysqli_real_escape_string($conn, $phone);
$doctor = mysqli_real_escape_string($conn, $doctor);
$appointment_date = mysqli_real_escape_string($conn, $appointment_date);
$appointment_time = mysqli_real_escape_string($conn, $appointment_time);


/* Check duplicate appointment */

$check_sql = "SELECT id
              FROM appointsment
              WHERE doctor = '$doctor'
              AND appointment_date = '$appointment_date'
              AND appointment_time = '$appointment_time'
              AND status != 'Cancelled'";

$check_result = mysqli_query($conn, $check_sql);

if (!$check_result) {
    die("Database error: " . mysqli_error($conn));
}

if (mysqli_num_rows($check_result) > 0) {

    die(
        "Sorry, this doctor is already booked " .
        "for this date and time."
    );
}


/* Save appointment */

$sql = "INSERT INTO appointsment
        (
            user_id,
            name,
            email,
            phone,
            doctor,
            appointment_date,
            appointment_time,
            status
        )
        VALUES
        (
            '$user_id',
            '$name',
            '$email',
            '$phone',
            '$doctor',
            '$appointment_date',
            '$appointment_time',
            'Pending'
        )";


/* Booking successful */

if (mysqli_query($conn, $sql)) {

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Appointment Booked - MediBook</title>

    <link rel="stylesheet"
          href="Css/Style.css">

</head>

<body>

<header>

    <nav class="navbar">

        <h1 class="logo">
            MediBook
        </h1>

    </nav>

</header>


<main>

<section class="services booking-success">

    <h2>
        Appointment Booked Successfully!
    </h2>

    <p class="success-message">
        Your appointment has been submitted successfully.
    </p>


    <div class="booking-details">

        <p>
            <strong>Patient:</strong>
            <?php echo htmlspecialchars($name); ?>
        </p>

        <p>
            <strong>Doctor:</strong>
            <?php echo htmlspecialchars($doctor); ?>
        </p>

        <p>
            <strong>Appointment Date:</strong>
            <?php echo htmlspecialchars($appointment_date); ?>
        </p>

        <p>
            <strong>Appointment Time:</strong>
            <?php echo htmlspecialchars($appointment_time); ?>
        </p>

        <p>
            <strong>Status:</strong>

            <span class="status-badge status-pending">
                Pending
            </span>

        </p>

    </div>


    <a href="my_appointment.php"
       class="book-btn">

        View My Appointments

    </a>

    <br><br>

    <a href="Pages/Appointment.html">

        Book Another Appointment

    </a>

</section>

</main>


<footer>

    <p>
        © 2026 MediBook. All Rights Reserved.
    </p>

</footer>

</body>

</html>

<?php

} else {

    echo "Error: " . mysqli_error($conn);

}

?>
```

<?php

session_start();

include "db.php";


/* Check login */

if (!isset($_SESSION['user_id'])) {
    header("Location: Pages/Login.html");
    exit();
}

$user_id = $_SESSION['user_id'];


/* Check POST data */

if (
    !isset($_POST['id']) ||
    !isset($_POST['doctor']) ||
    !isset($_POST['appointment_date']) ||
    !isset($_POST['appointment_time'])
) {
    die("Invalid request.");
}


$id = $_POST['id'];
$doctor = $_POST['doctor'];
$appointment_date = $_POST['appointment_date'];
$appointment_time = $_POST['appointment_time'];


/* Clean input */

$id = mysqli_real_escape_string($conn, $id);
$doctor = mysqli_real_escape_string($conn, $doctor);
$appointment_date = mysqli_real_escape_string($conn, $appointment_date);
$appointment_time = mysqli_real_escape_string($conn, $appointment_time);


/* Check that appointment belongs to this user */

$sql = "SELECT *
        FROM appointsment
        WHERE id = '$id'
        AND user_id = '$user_id'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}


if (mysqli_num_rows($result) != 1) {
    die("Appointment not found.");
}


$appointment = mysqli_fetch_assoc($result);


/* Prevent editing cancelled appointment */

if ($appointment['status'] === 'Cancelled') {
    die("This appointment has been cancelled and cannot be edited.");
}


/* Check duplicate doctor/date/time */

$check_sql = "SELECT id
              FROM appointsment
              WHERE doctor = '$doctor'
              AND appointment_date = '$appointment_date'
              AND appointment_time = '$appointment_time'
              AND status != 'Cancelled'
              AND id != '$id'";

$check_result = mysqli_query($conn, $check_sql);

if (!$check_result) {
    die("Database error: " . mysqli_error($conn));
}


if (mysqli_num_rows($check_result) > 0) {

    die(
        "Sorry, this doctor is already booked for this date and time."
    );
}


/* Update appointment */

$sql = "UPDATE appointsment
        SET doctor = '$doctor',
            appointment_date = '$appointment_date',
            appointment_time = '$appointment_time',
            status = 'Pending'
        WHERE id = '$id'
        AND user_id = '$user_id'";


if (mysqli_query($conn, $sql)) {

    header("Location: my_appointment.php");
    exit();

} else {

    echo "Error updating appointment: "
         . mysqli_error($conn);

}

?>
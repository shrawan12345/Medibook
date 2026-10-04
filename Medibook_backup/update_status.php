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


/* Check request */

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    die("Invalid request.");
}

$appointment_id = $_GET['id'];
$status = $_GET['status'];


/* Allow only these statuses */

$allowed_statuses = [
    'Pending',
    'Confirmed',
    'Cancelled'
];

if (!in_array($status, $allowed_statuses)) {
    die("Invalid status.");
}


/* Get appointment */

$sql = "SELECT *
        FROM appointsment
        WHERE id = '$appointment_id'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) != 1) {
    die("Appointment not found.");
}

$appointment = mysqli_fetch_assoc($result);

$doctor = $appointment['doctor'];
$date = $appointment['appointment_date'];
$time = $appointment['appointment_time'];


/* Check duplicate booking before confirming */

if ($status === 'Confirmed') {

    $check_sql = "SELECT id
                  FROM appointsment
                  WHERE doctor = '$doctor'
                  AND appointment_date = '$date'
                  AND appointment_time = '$time'
                  AND status = 'Confirmed'
                  AND id != '$appointment_id'";

    $check_result = mysqli_query($conn, $check_sql);

    if (!$check_result) {
        die("Database error: " . mysqli_error($conn));
    }


    if (mysqli_num_rows($check_result) > 0) {

        die(
            "Cannot confirm this appointment. " .
            "The doctor already has a confirmed appointment " .
            "at this date and time."
        );
    }
}


/* Update appointment status */

$sql = "UPDATE appointsment
        SET status = '$status'
        WHERE id = '$appointment_id'";


if (mysqli_query($conn, $sql)) {

    header("Location: admin.php");
    exit();

} else {

    echo "Error: " . mysqli_error($conn);

}

?>
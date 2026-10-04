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

/* Cancel only this user's appointment */

$sql = "UPDATE appointsment
        SET status = 'Cancelled'
        WHERE id = '$appointment_id'
        AND user_id = '$user_id'";

if (mysqli_query($conn, $sql)) {

    header("Location: my_appointment.php");
    exit();

} else {

    echo "Error: " . mysqli_error($conn);

}

?>
<?php

session_start();

include "db.php";


/* Check login */

if (!isset($_SESSION['user_id'])) {
    header("Location: Pages/Login.html");
    exit();
}

$user_id = $_SESSION['user_id'];


/* Check admin */

$sql = "SELECT role FROM users WHERE id = '$user_id'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result);

if (!$user || $user['role'] !== 'admin') {
    die("Access denied. Admins only.");
}


/* Check doctor ID */

if (!isset($_GET['id'])) {
    die("Doctor ID is missing.");
}

$id = $_GET['id'];


/* Check if doctor exists */

$sql = "SELECT name FROM doctors WHERE id = '$id'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) != 1) {
    die("Doctor not found.");
}

$doctor = mysqli_fetch_assoc($result);

$doctor_name = $doctor['name'];


/* Check existing appointments */

$sql = "SELECT id
        FROM appointsment
        WHERE doctor = '$doctor_name'
        AND status != 'Cancelled'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}


if (mysqli_num_rows($result) > 0) {

    die(
        "This doctor cannot be deleted because there are active appointments."
    );
}


/* Delete doctor */

$sql = "DELETE FROM doctors WHERE id = '$id'";

if (mysqli_query($conn, $sql)) {

    header("Location: admin_doctors.php");
    exit();

} else {

    echo "Error deleting doctor: "
         . mysqli_error($conn);

}

?>
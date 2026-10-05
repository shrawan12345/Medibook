<?php

include "db.php";

$sql = "SELECT * FROM doctors ORDER BY name";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {

    $nmc = trim($row['nmc_number']) !== ''
        ? $row['nmc_number']
        : 'Not provided';

    echo '<option value="' . $row['id'] . '">'
         . htmlspecialchars($row['name'])
         . ' - '
         . htmlspecialchars($row['specialization'])
         . ' (NMC: ' . htmlspecialchars($nmc) . ')'
         . '</option>';

}

?>
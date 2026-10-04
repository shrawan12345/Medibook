<?php

include "db.php";

$sql = "SELECT * FROM doctors ORDER BY name";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {

    echo '<option value="' . $row['id'] . '">'
         . htmlspecialchars($row['name'])
         . ' - '
         . htmlspecialchars($row['specialization'])
         . '</option>';

}

?>
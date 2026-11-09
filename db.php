
<?php
$conn = new mysqli("localhost", "root", "", "car_rental");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>

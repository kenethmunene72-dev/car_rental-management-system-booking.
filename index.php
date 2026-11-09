
<?php
include 'db.php';

$vehicles = $conn->query("SELECT COUNT(*) AS total FROM vehicles")->fetch_assoc()['total'];
$customers = $conn->query("SELECT COUNT(*) AS total FROM customers")->fetch_assoc()['total'];
$bookings = $conn->query("SELECT COUNT(*) AS total FROM bookings")->fetch_assoc()['total'];
$payments = $conn->query("SELECT COALESCE(SUM(amount),0) AS total FROM payments")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Car Rental System</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h1>CAR RENTAL MANAGEMENT SYSTEM</h1>

<div class="menu">
<a href="index.php">Dashboard</a>
<a href="vehicles.php">Vehicles</a>
<a href="customers.php">Customers</a>
<a href="bookings.php">Bookings</a>
<a href="payments.php">Payments</a>
</div>

<div class="container">

<h2>Dashboard</h2>

<div class="dashboard">
<div class="card">Vehicles: <?= $vehicles ?></div>
<div class="card">Customers: <?= $customers ?></div>
<div class="card">Bookings: <?= $bookings ?></div>
<div class="card">Payments: KSh <?= number_format($payments,2) ?></div>
</div>

</div>
</body>
</html>

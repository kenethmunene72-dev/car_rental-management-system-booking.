
<?php
include 'db.php';

if(isset($_POST['add'])) {
    $name = $_POST['car_name'];
    $reg = $_POST['registration_no'];
    $model = $_POST['model'];
    $rate = $_POST['daily_rate'];

    $stmt = $conn->prepare(
        "INSERT INTO vehicles (car_name, registration_no, model, daily_rate)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("sssd", $name, $reg, $model, $rate);

    if($stmt->execute()) {
        echo "<script>alert('Vehicle registered successfully!');</script>";
    } else {
        echo "<script>alert('Error: Registration number may already exist.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Vehicle Registration</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Vehicle Registration</h2>

<form method="POST">
<input type="text" name="car_name" placeholder="Car Name" required>
<input type="text" name="registration_no" placeholder="Registration Number" required>
<input type="text" name="model" placeholder="Car Model" required>
<input type="number" step="0.01" name="daily_rate" placeholder="Daily Rental Rate (KSh)" required>
<button name="add">Register Vehicle</button>
</form>

<a href="index.php">Back to Dashboard</a>

<h2>Registered Vehicles</h2>

<table>
<tr>
<th>ID</th>
<th>Car</th>
<th>Registration</th>
<th>Model</th>
<th>Daily Rate</th>
<th>Status</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM vehicles ORDER BY id DESC");

while($row = $result->fetch_assoc()) {
?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['car_name']) ?></td>
<td><?= htmlspecialchars($row['registration_no']) ?></td>
<td><?= htmlspecialchars($row['model']) ?></td>
<td>KSh <?= number_format($row['daily_rate'],2) ?></td>
<td><?= htmlspecialchars($row['status']) ?></td>
</tr>
<?php } ?>

</table>
</body>
</html>


<?php
include 'db.php';

$message = "";

if(isset($_POST['book'])) {

    $customer = (int)$_POST['customer_id'];
    $vehicle = (int)$_POST['vehicle_id'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];

    $startDate = new DateTime($start);
    $endDate = new DateTime($end);
    $today = new DateTime('today');

    $days = (int)$startDate->diff($endDate)->format('%r%a');

    if($startDate < $today || $days < 1) {
        $message = "Invalid booking dates!";
    } else {

        $stmt = $conn->prepare(
            "SELECT daily_rate FROM vehicles
             WHERE id = ? AND status = 'Available'"
        );
        $stmt->bind_param("i", $vehicle);
        $stmt->execute();
        $car = $stmt->get_result()->fetch_assoc();

        if($car) {

            $check = $conn->prepare(
                "SELECT id FROM bookings
                 WHERE vehicle_id = ?
                 AND status = 'Booked'
                 AND start_date < ?
                 AND end_date > ?"
            );

            $check->bind_param("iss", $vehicle, $end, $start);
            $check->execute();

            if($check->get_result()->num_rows > 0) {
                $message = "Sorry! This car is already booked for those dates.";
            } else {

                $total = $days * $car['daily_rate'];

                $stmt = $conn->prepare(
                    "INSERT INTO bookings
                    (customer_id, vehicle_id, start_date, end_date, total_amount)
                    VALUES (?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "iissd",
                    $customer,
                    $vehicle,
                    $start,
                    $end,
                    $total
                );

                if($stmt->execute()) {
                    $message = "SUCCESS! Your car has been booked successfully. Total: KSh " . number_format($total,2);
                } else {
                    $message = "Booking failed. Please try again.";
                }
            }

        } else {
            $message = "Vehicle is not available!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Car Booking</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Car Booking</h2>

<form method="POST">

<label>Select Customer</label>
<select name="customer_id" required>
<option value="">Choose Customer</option>

<?php
$result = $conn->query("SELECT * FROM customers");

while($row = $result->fetch_assoc()) {
?>
<option value="<?= $row['id'] ?>">
<?= htmlspecialchars($row['full_name']) ?>
</option>
<?php } ?>
</select>

<label>Select Vehicle</label>
<select name="vehicle_id" required>
<option value="">Choose Vehicle</option>

<?php
$result = $conn->query(
    "SELECT * FROM vehicles WHERE status='Available'"
);

while($row = $result->fetch_assoc()) {
?>
<option value="<?= $row['id'] ?>">
<?= htmlspecialchars($row['car_name']) ?>
- KSh <?= number_format($row['daily_rate'],2) ?>/day
</option>
<?php } ?>
</select>

<label>Start Date</label>
<input type="date" name="start_date" required>

<label>End Date</label>
<input type="date" name="end_date" required>

<button type="submit" name="book">Book Car</button>

</form>

<a href="index.php">Back to Dashboard</a>

<?php if($message != "") { ?>
<script>
alert(<?= json_encode($message) ?>);
</script>
<?php } ?>

<h2>Booking Records</h2>

<table>
<tr>
<th>Booking ID</th>
<th>Customer ID</th>
<th>Vehicle ID</th>
<th>Start Date</th>
<th>End Date</th>
<th>Total Amount</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM bookings ORDER BY id DESC");

while($row = $result->fetch_assoc()) {
?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['customer_id'] ?></td>
<td><?= $row['vehicle_id'] ?></td>
<td><?= $row['start_date'] ?></td>
<td><?= $row['end_date'] ?></td>
<td>KSh <?= number_format($row['total_amount'],2) ?></td>
</tr>
<?php } ?>

</table>

</body>
</html>

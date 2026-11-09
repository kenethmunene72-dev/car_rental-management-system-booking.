
<?php
include 'db.php';

$message = "";

if(isset($_POST['pay'])) {

    $booking = (int)$_POST['booking_id'];
    $amount = (float)$_POST['amount'];
    $method = $_POST['method'];

    $stmt = $conn->prepare(
        "SELECT total_amount FROM bookings
         WHERE id = ? AND status = 'Booked'"
    );
    $stmt->bind_param("i", $booking);
    $stmt->execute();
    $record = $stmt->get_result()->fetch_assoc();

    if(!$record || $amount <= 0) {
        $message = "Invalid booking or payment amount!";
    } else {

        $stmt = $conn->prepare(
            "INSERT INTO payments
            (booking_id, amount, payment_date, payment_method)
            VALUES (?, ?, CURDATE(), ?)"
        );

        $stmt->bind_param("ids", $booking, $amount, $method);

        if($stmt->execute()) {
            $message = "Payment recorded successfully!";
        } else {
            $message = "Payment failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Rental Payments</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Rental Payments</h2>

<form method="POST">

<label>Select Booking</label>

<select name="booking_id" required>
<option value="">Choose Booking</option>

<?php
$result = $conn->query(
    "SELECT * FROM bookings WHERE status='Booked'"
);

while($row = $result->fetch_assoc()) {
?>
<option value="<?= $row['id'] ?>">
Booking #<?= $row['id'] ?> - KSh <?= number_format($row['total_amount'],2) ?>
</option>
<?php } ?>
</select>

<input type="number" name="amount" step="0.01" min="0.01" placeholder="Amount Paid (KSh)" required>

<label>Payment Method</label>

<select name="method" required>
<option value="Cash">Cash</option>
<option value="M-Pesa">M-Pesa</option>
<option value="Bank Transfer">Bank Transfer</option>
</select>

<button name="pay">Record Payment</button>

</form>

<?php if($message != "") { ?>
<script>
alert(<?= json_encode($message) ?>);
</script>
<?php } ?>

<h2>Payment History</h2>

<table>
<tr>
<th>Payment ID</th>
<th>Booking ID</th>
<th>Amount</th>
<th>Date</th>
<th>Method</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM payments ORDER BY id DESC");

while($row = $result->fetch_assoc()) {
?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['booking_id'] ?></td>
<td>KSh <?= number_format($row['amount'],2) ?></td>
<td><?= $row['payment_date'] ?></td>
<td><?= htmlspecialchars($row['payment_method']) ?></td>
</tr>
<?php } ?>

</table>

<a href="index.php">Back to Dashboard</a>

</body>
</html>

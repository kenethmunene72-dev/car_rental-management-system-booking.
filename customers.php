
<?php
include 'db.php';

if(isset($_POST['add'])) {

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare(
        "INSERT INTO customers (full_name, phone, email, address)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param("ssss", $name, $phone, $email, $address);

    if($stmt->execute()) {
        echo "<script>alert('Customer registered successfully!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Customer Records</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Customer Registration</h2>

<form method="POST">
<input type="text" name="name" placeholder="Full Name" required>
<input type="text" name="phone" placeholder="Phone Number" required>
<input type="email" name="email" placeholder="Email Address" required>
<input type="text" name="address" placeholder="Address" required>

<button name="add">Register Customer</button>
</form>

<h2>Customer Records</h2>

<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Phone</th>
<th>Email</th>
<th>Address</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM customers ORDER BY id DESC");

while($row = $result->fetch_assoc()) {
?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['full_name']) ?></td>
<td><?= htmlspecialchars($row['phone']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
<td><?= htmlspecialchars($row['address']) ?></td>
</tr>
<?php } ?>

</table>

<a href="index.php">Back to Dashboard</a>

</body>
</html>

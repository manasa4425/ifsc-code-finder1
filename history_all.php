<?php
session_start();
include "db.php";

if (!isset($_SESSION['accno'])) {
    header("Location: customerlogin.php");
    exit;
}

$accno = $_SESSION['accno'];
$result = mysqli_query($conn,
    "SELECT * FROM transactions WHERE accno='$accno' ORDER BY date_time DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
<title>All Transactions</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
<h4>All Transactions</h4>

<table class="table table-bordered table-striped mt-3">
<tr>
    <th>Transaction ID</th>
    <th>Type</th>
    <th>Amount</th>
    <th>Date & Time</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['transaction_id']; ?></td>
    <td><?php echo ucfirst($row['type']); ?></td>
    <td>₹<?php echo $row['amount']; ?></td>
    <td><?php echo $row['datetime']; ?></td>
</tr>
<?php } ?>
</table>

<a href="history.php" class="btn btn-secondary">Back</a>
</div>

</body>
</html>
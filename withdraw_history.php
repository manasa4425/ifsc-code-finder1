<?php
session_start();
include "db.php";

if (!isset($_SESSION['accno'])) {
    header("Location: customerlogin.php");
    exit;
}

$accno = $_SESSION['accno'];

$result = mysqli_query($conn, "SELECT * FROM withdraws WHERE accno='$accno' ORDER BY date_time DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Withdraw History</title>
</head>
<body>

<h2>Withdraw History</h2>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Amount</th>
        <th>Reason</th>
        <th>Method</th>
        <th>Date Time</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['amount']; ?></td>
        <td><?php echo $row['reason']; ?></td>
        <td><?php echo $row['method']; ?></td>
        <td><?php echo $row['date_time']; ?></td>
    </tr>
    <?php } ?>
</table>

<br>
<a href="withdraw.php">Back</a>
<a href="dashboard.php">Dashboard</a>

</body>
</html>
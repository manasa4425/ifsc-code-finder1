<?php
session_start();
include "db.php";

if (!isset($_SESSION['accno'])) {
    header("Location: customerlogin.php");
    exit;
}

$accno = $_SESSION['accno'];

$sql = "SELECT * FROM transactions WHERE accno='$accno' AND type='deposit' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Deposit History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Deposit History</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Date & Time</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td>₹<?php echo $row['amount']; ?></td>
                <td><?php echo $row['date_time']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
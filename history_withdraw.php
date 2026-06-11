<?php
session_start();
include "db.php";

if (!isset($_SESSION['accno'])) {
    header("Location: customerlogin.php");
    exit;
}

$accno = $_SESSION['accno'];

$sql = "SELECT id, amount, date_time
        FROM transactions
        WHERE accno='$accno'
        AND (type='withdraw' OR type='withdrawal' OR type='debit')
        ORDER BY date_time DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Withdraw History</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<style>
body{
    background:#f1f2f6;
    font-family: Arial, sans-serif;
}
.box{
    width:90%;
    margin:40px auto;
    background:white;
    padding:20px;
    border-radius:15px;
    box-shadow:0 8px 25px rgba(0,0,0,0.1);
}
h4{
    text-align:center;
    margin-bottom:20px;
}
</style>
</head>

<body>

<div class="box">
<h4>Withdraw History</h4>

<table class="table table-bordered table-striped">
<thead class="thead-dark">
<tr>
    <th>Transaction ID</th>
    <th>Amount</th>
    <th>Date & Time</th>
</tr>
</thead>

<tbody>
<?php
if (mysqli_num_rows($result) == 0) {
    echo "<tr>
            <td colspan='3' class='text-center text-danger'>
                No Withdraw Records Found
            </td>
          </tr>";
}

while ($row = mysqli_fetch_assoc($result)) {
?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td>₹<?php echo $row['amount']; ?></td>
    <td><?php echo $row['date_time']; ?></td>
</tr>
<?php } ?>
</tbody>
</table>

<a href="transaction_history.php" class="btn btn-secondary">Back</a>
</div>

</body>
</html>
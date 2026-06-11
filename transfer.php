<?php
session_start();
include "db.php";

if (!isset($_SESSION['accno'])) {
    header("Location: customerlogin.php");
    exit;
}

$accno = $_SESSION['accno'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE accno='$accno'"));
$balance = $user['balance'];

$error = "";
$success = "";

if (isset($_POST['transfer'])) {
    $to_accno = $_POST['to_accno'];
    $amount = $_POST['amount'];

    if ($amount <= 0) {
        $error = "Enter valid amount";
    } else if ($amount > $balance) {
        $error = "Insufficient balance";
    } else {
        // Reduce sender balance
        $newBalance = $balance - $amount;
        mysqli_query($conn, "UPDATE customers SET balance='$newBalance' WHERE accno='$accno'");

        // Add receiver balance
        mysqli_query($conn, "UPDATE customers SET balance = balance + $amount WHERE accno='$to_accno'");

        // Insert transfer record
        mysqli_query($conn, "INSERT INTO transfers (accno, name, to_accno, amount) VALUES ('$accno', '".$user['name']."', '$to_accno', '$amount')");

        $success = "Transfer Successful";
        $balance = $newBalance;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transfer</title>
</head>
<body>
<h2>Transfer Money</h2>

<form method="post">
    <input type="text" name="to_accno" placeholder="Receiver Account Number" required><br><br>
    <input type="number" name="amount" placeholder="Amount" required><br><br>
    <button type="submit" name="transfer">Transfer</button>
</form>

<p style="color:red;"><?php echo $error; ?></p>
<p style="color:green;"><?php echo $success; ?></p>

<a href="dashboard.php">Back</a>
</body>
</html>
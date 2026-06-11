<?php
session_start();
include "db.php";

if (!isset($_SESSION['accno'])) {
    header("Location: customerlogin.php");
    exit;
}

$accno = $_SESSION['accno'];
$result = mysqli_query($conn, "SELECT * FROM customers WHERE accno='$accno'");
$user = mysqli_fetch_assoc($result);
?>

<?php include "header.php"; ?>   <!-- ✅ ADD THIS -->

<div class="container mt-5">
    <div class="header text-center mb-4">
        <h2>Welcome, <?php echo $user['name']; ?></h2>
        <p>Account Number: <b><?php echo $user['accno']; ?></b> | Balance: <b>₹<?php echo $user['balance']; ?></b></p>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <a href="profile.php" class="text-decoration-none">
                <div class="card p-4 text-center">
                    <h4>Profile</h4>
                    <p>View your account details</p>
                </div>
            </a>
        </div> 
        
        <div class="col-md-4 mb-3">
            <a href="withdraw.php" class="text-decoration-none">
                <div class="card p-4 text-center">
                    <h4>Withdraw</h4>
                    <p>Withdraw money from account</p>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a href="deposit.php" class="text-decoration-none">
                <div class="card p-4 text-center">
                    <h4>Deposit</h4>
                    <p>Add money to your account</p>
                </div>
            </a>
        </div>

        <div class="col-md-12 mb-3">
            <a href="history.php" class="text-decoration-none">
                <div class="card p-4 text-center">
                    <h4>History</h4>
                    <p>Withdraw + Deposit Records</p>
                </div>
            </a>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="logout1.php" class="btn btn-custom">Logout</a>
    </div>
</div>

</body>
</html>
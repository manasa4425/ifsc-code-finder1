<?php
session_start();
if (!isset($_SESSION['accno'])) {
    header("Location: customerlogin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaction History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ background:#f1f2f6; }
        .card{
            border-radius:15px;
            box-shadow:0 8px 20px rgba(0,0,0,0.1);
            transition:0.3s;
        }
        .card:hover{ transform:scale(1.03); }
    </style>
</head>
<body>

<div class="container mt-5">
    <h3 class="text-center mb-4">Transaction History</h3>

    <div class="row">
        <div class="col-md-4 mb-3">
            <a href="history_all.php" class="text-decoration-none">
                <div class="card p-4 text-center">
                    <h4>All Transactions</h4>
                    <p>Complete account statement</p>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a href="history_withdraw.php" class="text-decoration-none">
                <div class="card p-4 text-center">
                    <h4>Withdraw History</h4>
                    <p>Money debited records</p>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a href="history_deposit.php" class="text-decoration-none">
                <div class="card p-4 text-center">
                    <h4>Deposit History</h4>
                    <p>Money credited records</p>
                </div>
            </a>
        </div>
    </div>
</div>

</body>
</html>
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

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background: #f5f6fa;">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <!-- Profile Card -->
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="row">
                        <!-- Photo -->
                        <div class="col-md-4 text-center">
                            <img src="customer_photos/<?php echo $user['photo']; ?>" 
                                 class="img-fluid rounded-circle" 
                                 style="width: 170px; height: 170px; object-fit: cover;">
                        </div>

                        <!-- Profile Details -->
                        <div class="col-md-8">
                            <h3 class="font-weight-bold"><?php echo $user['name']; ?></h3>
                            <p><strong>Account No:</strong> <?php echo $user['accno']; ?></p>
                            <p><strong>Balance:</strong> ₹<?php echo $user['balance']; ?></p>
                            <p><strong>IFSC:</strong> <?php echo $user['ifsc']; ?></p>
                            <p><strong>Mobile:</strong> <?php echo $user['mobile']; ?></p>
                        </div>
                    </div>

                </div>
            </div>
            <!-- End Profile Card -->

        </div>
    </div>
</div>

</body>
</html>
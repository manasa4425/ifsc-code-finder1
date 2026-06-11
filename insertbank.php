<?php
include "db.php";

/* Prevent direct access */
if (!isset($_POST['s1'])) {
    header("Location: addbank.php");
    exit;
}

/* Collect form data */
$name     = $_POST['s1'];
$address  = $_POST['s2'];
$ifsc     = $_POST['s3'];
$phone    = $_POST['s4'];
$username = $_POST['s5'];
$password = $_POST['s6']; // ✅ plain text
$branch   = $_POST['s7'];
$state    = $_POST['s8'];
$city     = $_POST['s9'];
$district = $_POST['s10'];

/* ✅ Prepared statement (no hashing) */
$stmt = $conn->prepare(
    "INSERT INTO bank 
    (name, branch, address, state, district, city, ifsc, phone, username, password) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "ssssssssss",
    $name,
    $branch,
    $address,
    $state,
    $district,
    $city,
    $ifsc,
    $phone,
    $username,
    $password
);

/* Execute */
if ($stmt->execute()) {
    header("Location: ViewBank.php");
    exit;
} else {
    echo "Database Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

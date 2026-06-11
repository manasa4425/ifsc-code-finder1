<?php
session_start();
include "db.php";


if (!isset($_SESSION['accno'])) {
    header("Location: customerlogin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Customer Home | Bank IFSC</title>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  *{ box-sizing: border-box; margin: 0; padding: 0; }

  body{
    font-family: 'Nunito', sans-serif;
    background: linear-gradient(135deg, #0b2b5b, #0f4d8b, #0a7cff);
    min-height: 100vh;
  }

  /* ===== MENU OUTSIDE BLUR ===== */
  .menu {
    position: fixed;        /* fixed to top-left */
    top: 20px;
    left: 20px;
    z-index: 9999;          /* IMPORTANT: higher than blur */
  }

  .menu .dots {
    width: 45px;
    height: 45px;
    background: rgba(255,255,255,0.10);
    border: 1px solid rgba(255,255,255,0.30);
    border-radius: 12px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
  }

  .menu .dots i {
    color: #fff;
    font-size: 22px;
  }

  .menu .dropdown {
    display: none;
    position: absolute;
    top: 60px;
    left: 0;
    background: rgba(0,0,0,0.25);
    border: 1px solid rgba(255,255,255,0.30);
    border-radius: 12px;
    padding: 10px;
    width: 250px;
    backdrop-filter: blur(8px);
    z-index: 9999; /* keep on top */
  }

  .menu .dropdown a {
    display: block;
    color: #fff;
    text-decoration: none;
    padding: 10px;
    margin: 6px 0;
    border-radius: 10px;
    transition: 0.3s;
  }

  .menu .dropdown a:hover {
    background: rgba(255,255,255,0.12);
  }

  .container{
    width: 95%;
    max-width: 1200px;
    margin: 40px auto;
    background: rgba(255,255,255,0.10);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 16px;
    padding: 30px;
    backdrop-filter: blur(10px);
    box-shadow: 0 25px 60px rgba(0,0,0,0.45);
    position: relative;
  }

  .container:before{
    content:"";
    position: absolute;
    width: 240px;
    height: 240px;
    background: rgba(255,255,255,0.14);
    border-radius: 50%;
    top: -90px;
    right: -90px;
    filter: blur(20px);
  }

  .title{
    text-align: center;
    color: #fff;
    margin-bottom: 25px;
  }
  .title h1{
    font-size: 28px;
    font-weight: 700;
  }
  .title p{
    color: rgba(255,255,255,0.75);
    margin-top: 8px;
  }

  .grid{
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-top: 25px;
  }

  .card{
    background: rgba(255,255,255,0.10);
    padding: 25px;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 15px 40px rgba(0,0,0,0.25);
    transition: transform 0.3s ease;
  }

  .card:hover{
    transform: translateY(-8px);
  }

  .card h3{
    color: #fff;
    margin-bottom: 10px;
  }

  .card p{
    color: rgba(255,255,255,0.75);
    font-size: 14px;
  }

  .btn{
    display:inline-block;
    margin-top: 15px;
    padding: 10px 18px;
    border-radius: 12px;
    border: none;
    background: linear-gradient(90deg, #00d4ff, #1a9bff);
    color: #fff;
    font-weight: 700;
    text-decoration:none;
  }
</style>
</head>

<body>

<div class="menu">
  <div class="dots" onClick="toggleMenu()">
    <i class="fa fa-bars"></i>
  </div>

  <div class="dropdown" id="menuDropdown">
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
    <a href="adminlogin.php">Admin Login</a>
    <a href="banklogin.php">Bank Login</a>
    <a href="customerlogin.php">Customer Login</a>
    <a href="contact.php">Contact</a>
  </div>
</div>

<div class="container">
  <div class="title">
    <h1>Welcome Customer</h1>
    <p>Choose your option below</p>
  </div>

  <div class="grid">
    <div class="card">
      <h3>Profile</h3>
      <p>View your personal details</p>
      <a href="customer_profile.php" class="btn">Open</a>
    </div>

    <div class="card">
      <h3>Withdraw</h3>
      <p>Withdraw money from your account</p>
      <a href="customer_withdraw.php" class="btn">Open</a>
    </div>

    <div class="card">
      <h3>Deposit</h3>
      <p>Deposit money into your account</p>
      <a href="customer_deposit.php" class="btn">Open</a>
    </div>

    <div class="card">
      <h3>History</h3>
      <p>View transaction history</p>
      <a href="customer_history.php" class="btn">Open</a>
    </div>

    <div class="card">
      <h3>Logout</h3>
      <p>Exit from your account</p>
      <a href="customer_logout.php" class="btn">Logout</a>
    </div>
  </div>
</div>

<script>
function toggleMenu() {
  var menu = document.getElementById("menuDropdown");
  menu.style.display = (menu.style.display === "block") ? "none" : "block";
}
</script>

</body>
</html>
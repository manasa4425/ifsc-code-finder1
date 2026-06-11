<?php
session_start();
include "db.php";

// Check login
if (!isset($_SESSION['accno'])) {
    header("Location: customerlogin.php");
    exit;
}

$accno = $_SESSION['accno'];

// Handle profile photo upload
if (isset($_POST['upload_photo'])) {
    $target_dir = "uploads/profile/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_name = time() . '_' . basename($_FILES["profile_photo"]["name"]);
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is actual image
    $check = getimagesize($_FILES["profile_photo"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $error = "File is not an image.";
        $uploadOk = 0;
    }
    
    // Check file size (max 2MB)
    if ($_FILES["profile_photo"]["size"] > 2000000) {
        $error = "File is too large. Max 2MB allowed.";
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    
    // Upload file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
            // Update database with photo path
            $update_photo = mysqli_query($conn, "UPDATE account SET profile_photo='$target_file' WHERE accno='$accno'");
            if ($update_photo) {
                // Log profile edit activity
                mysqli_query($conn, "INSERT INTO profile_activity (accountno, activity_type, description, date) VALUES ('$accno', 'photo_update', 'Profile photo updated', NOW())");
                $success = "Profile photo uploaded successfully!";
            }
        } else {
            $error = "Error uploading file.";
        }
    }
}

// FETCH PROFILE DATA
$result = mysqli_query($conn, "SELECT * FROM account WHERE accno='$accno'");
$user = mysqli_fetch_assoc($result);

// Get transaction stats
$total_deposits = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(deposite) as total FROM transactions WHERE accountno='$accno'"))['total'] ?? 0;
$total_withdrawals = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(withdraw) as total FROM transactions WHERE accountno='$accno'"))['total'] ?? 0;
$transaction_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transactions WHERE accountno='$accno'"));
$account_age = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MIN(date) as first FROM transactions WHERE accountno='$accno'"))['first'] ?? date('Y-m-d');

// Get profile edit activity
$profile_activity = mysqli_query($conn, "SELECT * FROM profile_activity WHERE accountno='$accno' ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Customer Profile | Bank IFSC Finder</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Google Fonts - Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px;
    position: relative;
    overflow-x: hidden;
}

/* Background Pattern */
.background-pattern {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    z-index: -1;
}

.background-pattern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.1"><path d="M10 10 L90 10 L90 90 L10 90 Z" fill="none" stroke="white" stroke-width="1"/><path d="M20 20 L80 20 L80 80 L20 80 Z" fill="none" stroke="white" stroke-width="1"/><path d="M30 30 L70 30 L70 70 L30 70 Z" fill="none" stroke="white" stroke-width="1"/></svg>');
    background-size: 100px 100px;
    animation: movePattern 20s linear infinite;
}

@keyframes movePattern {
    0% { background-position: 0 0; }
    100% { background-position: 100px 100px; }
}

/* Header */
.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 24px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    position: relative;
    z-index: 100;
}

.header-left {
    display: flex;
    align-items: center;
}

.three-dots {
    font-size: 20px;
    cursor: pointer;
    margin-right: 20px;
    color: #2563eb;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.2s;
}

.three-dots:hover {
    background: #eff6ff;
}

.logo {
    font-size: 22px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.logo i {
    color: #2563eb;
    font-size: 24px;
}

.logo span {
    color: #2563eb;
}

/* Header Navigation Links */
.header-nav {
    display: flex;
    gap: 12px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.3s;
}

.nav-link.withdraw {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(220,38,38,0.3);
}

.nav-link.deposit {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(16,185,129,0.3);
}

.nav-link.history {
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(139,92,246,0.3);
}

.nav-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2);
}

/* Dropdown Menu */
.dropdown {
    position: absolute;
    top: 70px;
    left: 20px;
    width: 240px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    border: 1px solid #e2e8f0;
    display: none;
    z-index: 1000;
}

.dropdown.show {
    display: block;
    animation: slideDown 0.2s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    color: #334155;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s;
}

.dropdown a:hover {
    background: #f8fafc;
    color: #2563eb;
    padding-left: 28px;
}

.dropdown a i {
    width: 20px;
    color: #64748b;
}

.dropdown a:hover i {
    color: #2563eb;
}

.dropdown a:last-child {
    border-bottom: none;
}

/* Main Container */
.main-container {
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

/* Welcome Card */
.welcome-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    gap: 30px;
    flex-wrap: wrap;
    animation: fadeInUp 0.6s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Profile Photo Section */
.photo-section {
    position: relative;
    text-align: center;
}

.profile-photo-container {
    width: 120px;
    height: 120px;
    border-radius: 60px;
    background: linear-gradient(135deg, #2563eb, #1e40af);
    padding: 4px;
    box-shadow: 0 10px 25px -5px rgba(37,99,235,0.4);
    position: relative;
}

.profile-photo {
    width: 100%;
    height: 100%;
    border-radius: 58px;
    object-fit: cover;
    border: 3px solid white;
}

.default-photo {
    width: 100%;
    height: 100%;
    border-radius: 58px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: #2563eb;
    font-weight: 600;
}

.photo-upload-btn {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    border: 2px solid white;
    transition: all 0.3s;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);
}

.photo-upload-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 10px 15px -3px rgba(139,92,246,0.4);
}

.photo-upload-btn i {
    font-size: 16px;
}

#photo-input {
    display: none;
}

.welcome-content {
    flex: 1;
}

.welcome-content h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 5px;
}

.welcome-content p {
    color: #64748b;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.welcome-badge {
    background: #dbeafe;
    color: #2563eb;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.welcome-badge i {
    font-size: 12px;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 22px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #2563eb, #1e40af);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    margin-bottom: 15px;
}

.stat-label {
    color: #64748b;
    font-size: 13px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 5px;
}

.stat-value {
    color: #1e293b;
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 3px;
}

.stat-desc {
    color: #94a3b8;
    font-size: 12px;
}

/* Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-bottom: 25px;
}

/* Cards */
.card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header h3 i {
    color: #2563eb;
    font-size: 20px;
}

.card-body {
    padding: 24px;
}

/* Info Rows */
.info-row {
    display: flex;
    padding: 14px 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    width: 140px;
    color: #64748b;
    font-size: 14px;
    font-weight: 500;
}

.info-value {
    flex: 1;
    color: #1e293b;
    font-size: 14px;
    font-weight: 600;
}

.info-value.sensitive {
    font-family: monospace;
    letter-spacing: 1px;
    background: #f8fafc;
    padding: 4px 10px;
    border-radius: 6px;
    display: inline-block;
}

/* Quick Actions */
.quick-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 20px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(37,99,235,0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(37,99,235,0.4);
}

.btn-secondary {
    background: white;
    color: #334155;
    border: 2px solid #e2e8f0;
}

.btn-secondary:hover {
    background: #f8fafc;
    border-color: #2563eb;
    color: #2563eb;
    transform: translateY(-2px);
}

/* Purple Gradient for Other Services */
.btn-purple {
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.3);
}

.btn-purple:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(139, 92, 246, 0.4);
}

/* Red Gradient for Logout */
.btn-danger {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(220,38,38,0.3);
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(220,38,38,0.4);
}

/* Recent Activity Section */
.recent-activity {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 24px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.activity-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.activity-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 10px;
}

.activity-header h3 i {
    color: #2563eb;
}

.activity-tabs {
    display: flex;
    gap: 10px;
}

.activity-tab {
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    background: #f1f5f9;
    color: #64748b;
    transition: all 0.3s;
}

.activity-tab.active {
    background: #2563eb;
    color: white;
}

.activity-content {
    display: none;
}

.activity-content.active {
    display: block;
}

.activity-list {
    list-style: none;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f1f5f9;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    background: #f1f5f9;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.activity-icon.transaction {
    color: #2563eb;
}

.activity-icon.profile {
    color: #8b5cf6;
}

.activity-details {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: #1e293b;
    font-size: 14px;
    margin-bottom: 3px;
}

.activity-time {
    color: #94a3b8;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.activity-amount {
    font-weight: 700;
    font-size: 16px;
}

.activity-amount.credit {
    color: #059669;
}

.activity-amount.debit {
    color: #dc2626;
}

.activity-badge {
    background: #8b5cf6;
    color: white;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

/* Security Note */
.security-note {
    margin-top: 25px;
    text-align: center;
    color: rgba(255,255,255,0.8);
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.security-note i {
    color: #10b981;
}

/* Footer Note */
.footer-note {
    position: fixed;
    bottom: 25px;
    right: 35px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(5px);
    padding: 8px 20px;
    border-radius: 30px;
    color: #1e293b;
    font-size: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    z-index: 100;
}

.footer-note i {
    color: #10b981;
    margin-right: 5px;
}

/* Live Date */
.live-date {
    position: fixed;
    bottom: 25px;
    left: 35px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(5px);
    padding: 8px 20px;
    border-radius: 30px;
    color: #1e293b;
    font-size: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    z-index: 100;
}

.live-date i {
    color: #2563eb;
    margin-right: 5px;
}

/* Alert Messages */
.alert {
    padding: 12px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideIn 0.3s;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.alert.success {
    background: #d1fae5;
    color: #059669;
    border-left: 4px solid #10b981;
}

.alert.error {
    background: #fee2e2;
    color: #dc2626;
    border-left: 4px solid #ef4444;
}

/* Responsive */
@media (max-width: 1024px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    body {
        padding: 15px;
    }
    
    .header {
        flex-direction: column;
        gap: 15px;
    }
    
    .header-nav {
        width: 100%;
        justify-content: center;
    }
    
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .welcome-card {
        flex-direction: column;
        text-align: center;
    }
    
    .info-row {
        flex-direction: column;
        gap: 5px;
    }
    
    .info-label {
        width: auto;
    }
    
    .quick-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .footer-note, .live-date {
        bottom: 15px;
        padding: 5px 15px;
        font-size: 10px;
    }
    
    .footer-note {
        right: 15px;
    }
    
    .live-date {
        left: 15px;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .activity-tabs {
        flex-direction: column;
        width: 100%;
    }
    
    .activity-tab {
        text-align: center;
    }
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    border-radius: 10px;
}
</style>
</head>
<body>

<!-- Background Pattern -->
<div class="background-pattern"></div>

<!-- Header -->
<div class="header">
    <div class="header-left">
        <div class="three-dots" onclick="toggleMenu()">
            <i class="fas fa-ellipsis-v"></i>
        </div>

        <div class="logo">
            <i class="fas fa-building-columns"></i> BANK <span>IFSC</span>
        </div>
    </div>

    <!-- Header Navigation Links -->
    <div class="header-nav">
        <a href="cwithdraw.php" class="nav-link withdraw">
            <i class="fas fa-arrow-up"></i> Withdraw
        </a>
        <a href="cdeposit.php" class="nav-link deposit">
            <i class="fas fa-arrow-down"></i> Deposit
        </a>
        <a href="chistory.php" class="nav-link history">
            <i class="fas fa-history"></i> History
        </a>
    </div>

    <div class="dropdown" id="menu">
        <a href="customerhome.php"><i class="fas fa-home"></i> Home</a>
        <a href="customer_profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="clogout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<!-- Main Container -->
<div class="main-container">
    
    <?php if(isset($success)): ?>
        <div class="alert success">
            <i class="fas fa-check-circle"></i>
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <?php if(isset($error)): ?>
        <div class="alert error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- Welcome Card with Profile Photo -->
    <div class="welcome-card">
        <div class="photo-section">
            <div class="profile-photo-container">
                <?php if(!empty($user['profile_photo']) && file_exists($user['profile_photo'])): ?>
                    <img src="<?php echo $user['profile_photo']; ?>" alt="Profile Photo" class="profile-photo">
                <?php else: ?>
                    <div class="default-photo">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Photo Upload Button -->
                <form method="post" enctype="multipart/form-data" id="photo-form">
                    <label for="photo-input" class="photo-upload-btn">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" id="photo-input" name="profile_photo" accept="image/*" onchange="document.getElementById('photo-form').submit();">
                    <input type="hidden" name="upload_photo" value="1">
                </form>
            </div>
        </div>
        
        <div class="welcome-content">
            <h1>Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</h1>
            <p>
                <span><i class="fas fa-envelope" style="color: #2563eb;"></i> <?php echo htmlspecialchars($user['email']); ?></span>
                <span><i class="fas fa-phone" style="color: #2563eb;"></i> <?php echo htmlspecialchars($user['phone']); ?></span>
                <span class="welcome-badge">
                    <i class="fas fa-check-circle"></i> Verified Account
                </span>
            </p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-label">Account Balance</div>
            <div class="stat-value">₹<?php echo number_format($user['balance']); ?></div>
            <div class="stat-desc">Available balance</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-label">Total Deposits</div>
            <div class="stat-value">₹<?php echo number_format($total_deposits); ?></div>
            <div class="stat-desc">Lifetime deposits</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-label">Total Withdrawals</div>
            <div class="stat-value">₹<?php echo number_format($total_withdrawals); ?></div>
            <div class="stat-desc">Lifetime withdrawals</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="stat-label">Transactions</div>
            <div class="stat-value"><?php echo $transaction_count; ?></div>
            <div class="stat-desc">Total transactions</div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Account Details Card -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-id-card"></i> Account Details</h3>
                <a href="profile.php?edit=<?php echo $user['accno']; ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Account Number</span>
                    <span class="info-value sensitive"><?php echo $user['accno']; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">IFSC Code</span>
                    <span class="info-value sensitive"><?php echo $user['ifsc']; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Bank Name</span>
                    <span class="info-value"><?php echo $user['bankname']; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Account Type</span>
                    <span class="info-value">Savings Account</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Member Since</span>
                    <span class="info-value"><?php echo date('d M Y', strtotime($account_age)); ?></span>
                </div>
            </div>
        </div>

        <!-- Personal Information Card -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-user"></i> Personal Information</h3>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Full Name</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['name']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email Address</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Mobile Number</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['phone']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Gender</span>
                    <span class="info-value"><?php echo $user['gender']; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Aadhar Number</span>
                    <span class="info-value sensitive">XXXX-XXXX-<?php echo substr($user['adharno'], -4); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Address</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['address']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity with Profile Edits -->
    <div class="recent-activity">
        <div class="activity-header">
            <h3><i class="fas fa-clock"></i> Recent Activity</h3>
            <div class="activity-tabs">
                <span class="activity-tab active" onclick="showActivity('transactions')">Transactions</span>
                <span class="activity-tab" onclick="showActivity('profile')">Profile Edits</span>
            </div>
        </div>
        
        <!-- Transactions Activity -->
        <div id="transactions-activity" class="activity-content active">
            <div class="activity-list">
                <?php
                $recent = mysqli_query($conn, "SELECT * FROM transactions WHERE accountno='$accno' ORDER BY id DESC LIMIT 5");
                if (mysqli_num_rows($recent) > 0) {
                    while($row = mysqli_fetch_assoc($recent)) {
                        if ($row['deposite'] > 0) {
                            $type = 'Deposit';
                            $icon = 'fa-arrow-down';
                            $amount = '+ ₹' . number_format($row['deposite']);
                            $class = 'credit';
                        } elseif ($row['withdraw'] > 0) {
                            $type = 'Withdrawal';
                            $icon = 'fa-arrow-up';
                            $amount = '- ₹' . number_format($row['withdraw']);
                            $class = 'debit';
                        } elseif (!empty($row['transfer_from'])) {
                            $type = 'Transfer';
                            $icon = 'fa-exchange-alt';
                            $amount = '₹' . number_format($row['amount']);
                            $class = 'transfer';
                        }
                ?>
                <div class="activity-item">
                    <div class="activity-icon transaction">
                        <i class="fas <?php echo $icon; ?>"></i>
                    </div>
                    <div class="activity-details">
                        <div class="activity-title"><?php echo $type; ?></div>
                        <div class="activity-time">
                            <i class="fas fa-calendar"></i> <?php echo date('d M Y, h:i A', strtotime($row['date'] ?? 'now')); ?>
                        </div>
                    </div>
                    <div class="activity-amount <?php echo $class; ?>"><?php echo $amount; ?></div>
                </div>
                <?php 
                    }
                } else {
                ?>
                <div style="text-align: center; padding: 30px; color: #94a3b8;">
                    <i class="fas fa-history" style="font-size: 40px; margin-bottom: 10px;"></i>
                    <p>No recent transactions</p>
                </div>
                <?php } ?>
            </div>
        </div>
        
        <!-- Profile Edit Activity -->
        <div id="profile-activity" class="activity-content">
            <div class="activity-list">
                <?php
                if (mysqli_num_rows($profile_activity) > 0) {
                    while($activity = mysqli_fetch_assoc($profile_activity)) {
                        $icon = ($activity['activity_type'] == 'photo_update') ? 'fa-camera' : 'fa-user-edit';
                ?>
                <div class="activity-item">
                    <div class="activity-icon profile">
                        <i class="fas <?php echo $icon; ?>"></i>
                    </div>
                    <div class="activity-details">
                        <div class="activity-title"><?php echo $activity['description']; ?></div>
                        <div class="activity-time">
                            <i class="fas fa-calendar"></i> <?php echo date('d M Y, h:i A', strtotime($activity['date'])); ?>
                        </div>
                    </div>
                    <span class="activity-badge">Profile</span>
                </div>
                <?php 
                    }
                } else {
                ?>
                <div style="text-align: center; padding: 30px; color: #94a3b8;">
                    <i class="fas fa-user-edit" style="font-size: 40px; margin-bottom: 10px;"></i>
                    <p>No profile edit activity yet</p>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions - Services and Logout -->
    <div class="quick-actions">
        <a href="services.php" class="btn btn-purple">
            <i class="fas fa-cog"></i> Other Services
        </a>
        <a href="clogout.php" class="btn btn-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <!-- Security Note -->
    <div class="security-note">
        <i class="fas fa-shield-alt"></i>
        <span>Your account is protected with 256-bit encryption</span>
        <i class="fas fa-lock"></i>
    </div>
</div>

<!-- Live Date -->
<div class="live-date" id="live-date">
    <i class="fas fa-calendar-alt"></i> Loading...
</div>

<!-- Footer Note -->
<div class="footer-note">
    <i class="fas fa-shield-alt"></i> Secure Banking • Your privacy is protected
</div>

<script>
function toggleMenu() {
    const menu = document.getElementById("menu");
    menu.classList.toggle('show');
}

// Close menu when clicking outside
document.addEventListener("click", function(e) {
    const menu = document.getElementById("menu");
    const dots = document.querySelector(".three-dots");
    
    if (!menu.contains(e.target) && !dots.contains(e.target)) {
        menu.classList.remove('show');
    }
});

// Live Date
function updateDate() {
    const now = new Date();
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    const dateStr = now.toLocaleDateString('en-US', options);
    document.getElementById('live-date').innerHTML = '<i class="fas fa-calendar-alt"></i> ' + dateStr;
}
setInterval(updateDate, 1000);
updateDate();

// Activity tabs
function showActivity(type) {
    // Hide all activity content
    document.getElementById('transactions-activity').classList.remove('active');
    document.getElementById('profile-activity').classList.remove('active');
    
    // Remove active class from all tabs
    document.querySelectorAll('.activity-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Show selected activity
    if (type === 'transactions') {
        document.getElementById('transactions-activity').classList.add('active');
        document.querySelectorAll('.activity-tab')[0].classList.add('active');
    } else {
        document.getElementById('profile-activity').classList.add('active');
        document.querySelectorAll('.activity-tab')[1].classList.add('active');
    }
}

// Preview uploaded image
document.getElementById('photo-input').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            // Optional: Show preview
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>

</body>
</html>
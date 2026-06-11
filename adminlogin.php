<?php
session_start();
require_once 'db_config.php';

$error = "";

// Check if already logged in as admin
if (isset($_SESSION['admin'])) {
    header("Location: admindashboard.php");
    exit();
}

// For showing username in header (if any user is logged in from main site)
$username = '';
$full_name = '';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'] ?? '';
    $full_name = $_SESSION['full_name'] ?? '';
}

// Get unread security alerts count (if user is logged in)
$alert_count = 0;
if (isset($_SESSION['user_id'])) {
    $alerts_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM security_alerts WHERE user_id = $user_id AND is_read = FALSE");
    if ($alerts_query) {
        $alert_count = mysqli_fetch_assoc($alerts_query)['count'];
    }
}

if (isset($_POST['login'])) {
    include "db.php";
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM login WHERE username='$u' AND password='$p'";
    $res = mysqli_query($conn, $sql);

    if (!$res) {
        die("Query Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($res) == 1) {
        $_SESSION['admin'] = $u;
        header("Location: admindashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login | Bank IFSC Finder</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 15px;
}

/* Header - EXACT same as about.php */
.header {
    display: flex;
    align-items: center;
    padding: 10px 20px;
    background: white;
    border-radius: 12px;
    margin-bottom: 15px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    position: relative;
    z-index: 100;
}

.three-dots {
    font-size: 24px;
    cursor: pointer;
    margin-right: 20px;
    color: #2563EB;
    transition: all 0.3s;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.three-dots:hover {
    background: #EFF6FF;
    transform: scale(1.1);
}

.logo {
    font-size: 24px;
    font-weight: bold;
    color: #1A2B4C;
    flex-shrink: 0;
}

.logo span {
    color: #2563EB;
    font-size: 28px;
}

.logo i {
    color: #2563EB;
    margin-right: 8px;
}

/* User Info Section - Only shown if logged in */
.user-info {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-details {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #F8FAFC;
    padding: 6px 15px;
    border-radius: 30px;
    border: 1px solid #E2E8F0;
    white-space: nowrap;
}

.user-details i {
    color: #2563EB;
    font-size: 16px;
}

.user-details span {
    color: #1E293B;
    font-size: 14px;
    font-weight: 500;
}

.security-link {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #EFF6FF;
    color: #2563EB;
    text-decoration: none;
    padding: 6px 15px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
    border: 1px solid #2563EB;
    transition: all 0.3s;
    white-space: nowrap;
}

.security-link:hover {
    background: #2563EB;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(37, 99, 235, 0.2);
}

.alert-badge {
    background: #DC2626;
    color: white;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 12px;
    margin-left: 5px;
    font-weight: 600;
}

.logout-btn {
    background: #DC2626;
    color: white;
    text-decoration: none;
    padding: 6px 15px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.logout-btn:hover {
    background: #B91C1C;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(220, 38, 38, 0.2);
}

/* Dropdown Menu - EXACT same as about.php */
.dropdown {
    position: absolute;
    top: 65px;
    left: 20px;
    width: 280px;
    background: #0f4c75;
    border-radius: 12px;
    display: none;
    box-shadow: 0 10px 25px rgba(0,0,0,0.4);
    overflow: hidden;
    z-index: 1000;
    border: 1px solid rgba(255,255,255,0.1);
    animation: slideDown 0.3s ease;
}

/* User section in dropdown */
.dropdown-user {
    background: #1e3c5c;
    padding: 15px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.dropdown-user .user-name {
    color: white;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 5px;
}

.dropdown-user .user-email {
    color: #a0c0e0;
    font-size: 12px;
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
    padding: 15px 18px;
    color: #fff;
    text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    transition: all 0.3s;
    font-size: 15px;
}

.dropdown a:hover {
    background: #3282b8;
    padding-left: 25px;
}

.dropdown a i {
    width: 20px;
    color: #00FFD1;
}

.dropdown a:last-child {
    border-bottom: none;
}

.dropdown.show {
    display: block;
}

/* Main Container - Full height flex */
.main-container {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

/* Background Pattern */
.background-pattern {
    position: absolute;
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

/* Login Container */
.login-container {
    width: 100%;
    max-width: 450px;
    padding: 20px;
    position: relative;
    z-index: 1;
    animation: fadeInUp 0.6s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.login-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Logo inside card */
.card-logo {
    text-align: center;
    margin-bottom: 30px;
}

.card-logo i {
    font-size: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 10px;
}

.card-logo h1 {
    color: #1E293B;
    font-size: 24px;
    font-weight: 600;
}

.card-logo p {
    color: #64748B;
    font-size: 14px;
    margin-top: 5px;
}

/* Login Header */
.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.login-header h2 {
    color: #1E293B;
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 10px;
}

.login-header h2 i {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-right: 10px;
}

.login-header p {
    color: #64748B;
    font-size: 14px;
}

/* Input Groups */
.input-group {
    position: relative;
    margin-bottom: 20px;
}

.input-group i {
    position: absolute;
    top: 50%;
    left: 15px;
    transform: translateY(-50%);
    color: #667eea;
    font-size: 16px;
    z-index: 1;
    transition: color 0.3s;
}

input {
    width: 100%;
    padding: 15px 15px 15px 45px;
    border: 2px solid #E2E8F0;
    border-radius: 12px;
    outline: none;
    background: white;
    color: #1E293B;
    font-size: 15px;
    transition: all 0.3s;
}

input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}

input:focus + i {
    color: #764ba2;
}

input::placeholder {
    color: #94A3B8;
}

/* Login Button */
.login-btn {
    width: 100%;
    padding: 15px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
    position: relative;
    overflow: hidden;
}

.login-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.login-btn:hover::before {
    left: 100%;
}

.login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
}

.login-btn:active {
    transform: translateY(0);
}

/* Error Message */
.error-message {
    background: #FEF2F2;
    border-left: 4px solid #DC2626;
    color: #DC2626;
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.error-message i {
    font-size: 16px;
}

/* Links */
.links {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 25px;
    padding-top: 25px;
    border-top: 1px solid #E2E8F0;
}

.links a {
    color: #667eea;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.links a:hover {
    color: #764ba2;
    transform: translateX(-3px);
}

.links a i {
    font-size: 12px;
}

/* Security Badge */
.security-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
    padding: 10px;
    background: #F8FAFC;
    border-radius: 30px;
    color: #64748B;
    font-size: 12px;
}

.security-badge i {
    color: #10B981;
}

.security-badge span {
    color: #1E293B;
    font-weight: 600;
}

/* Live Clock */
.live-clock {
    position: fixed;
    bottom: 25px;
    right: 35px;
    background: white;
    padding: 8px 20px;
    border-radius: 30px;
    font-weight: 600;
    color: #1E293B;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    z-index: 1000;
    font-size: 14px;
    border: 1px solid rgba(255,255,255,0.2);
    backdrop-filter: blur(5px);
    background: rgba(255, 255, 255, 0.95);
}

.live-clock i {
    color: #667eea;
    margin-right: 5px;
}

/* Footer Note */
.footer-note {
    position: fixed;
    bottom: 25px;
    left: 35px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(5px);
    padding: 8px 20px;
    border-radius: 30px;
    color: #1E293B;
    font-size: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    border: 1px solid rgba(255,255,255,0.2);
}

.footer-note i {
    color: #10B981;
    margin-right: 5px;
}

/* Responsive */
@media (max-width: 768px) {
    .login-container {
        padding: 15px;
    }
    
    .login-card {
        padding: 30px 20px;
    }
    
    .logo {
        font-size: 18px;
    }
    
    .logo span {
        font-size: 22px;
    }
    
    .user-info {
        max-width: 50%;
    }
    
    .links {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
}
</style>
</head>
<body>

<!-- HEADER - EXACT same as about.php -->
<div class="header">
    <div class="three-dots" onclick="toggleMenu()">
        <i class="fa-solid fa-ellipsis-vertical"></i>
    </div>

    <div class="logo">
        <i class="fa-solid fa-building-columns"></i> BANK <span>IFSC Finder</span>
    </div>

    <!-- User Info Section - Only show if user is logged in from main site -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="user-info">
        <div class="user-details">
            <i class="fas fa-user-circle"></i>
            <span><?php echo htmlspecialchars($full_name ?: $username); ?></span>
        </div>
        
        <a href="security.php" class="security-link">
            <i class="fas fa-shield-alt"></i> <span>Security</span>
            <?php if ($alert_count > 0): ?>
                <span class="alert-badge"><?php echo $alert_count; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="logout2.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
        </a>
    </div>
    <?php endif; ?>

    <!-- Dropdown Menu - EXACT same as about.php -->
    <div class="dropdown" id="menu">
        <!-- Show user info in dropdown if logged in -->
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="dropdown-user">
            <div class="user-name"><?php echo htmlspecialchars($full_name ?: $username); ?></div>
            <div class="user-email">Logged in</div>
        </div>
        <?php endif; ?>
        
        <a href="index.php"><i class="fa fa-home"></i> Home</a>
        <a href="security.php">
            <i class="fa fa-shield-halved"></i> Security Panel
            <?php if ($alert_count > 0): ?>
                <span style="background:#DC2626;color:white;padding:2px 8px;border-radius:10px;margin-left:5px;">
                    <?php echo $alert_count; ?>
                </span>
            <?php endif; ?>
        </a>
        <a href="about.php"><i class="fa fa-info-circle"></i> About</a>
        <a href="adminlogin.php"><i class="fa fa-user-shield"></i> Admin Login</a>
        <a href="banklogin.php"><i class="fa fa-university"></i> Bank Login</a>
        <a href="customerlogin.php"><i class="fa fa-users"></i> Customer Login</a>
        <a href="bankdetails.php"><i class="fa fa-building-columns"></i> Bank Details</a>
        <a href="contact.php"><i class="fa fa-phone"></i> Contact</a>
        <a href="settings.php"><i class="fa fa-gear"></i> Settings</a>
        
        <!-- Show logout in dropdown if user is logged in -->
        <?php if (isset($_SESSION['user_id'])): ?>
        <a href="logout2.php" style="background:#DC2626;"><i class="fa fa-sign-out-alt"></i> Logout</a>
        <?php endif; ?>
    </div>
</div>

<!-- MAIN CONTAINER -->
<div class="main-container">
    <!-- Background Pattern -->
    <div class="background-pattern"></div>
    
    <!-- Login Container -->
    <div class="login-container">
        <div class="login-card">
            <div class="card-logo">
                <i class="fas fa-building-columns"></i>
                <h1>BANK IFSC Finder</h1>
                <p>Secure Banking Solutions</p>
            </div>

            <?php if($error != ""): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="input-group">
                    <i class="fa fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required autofocus>
                </div>

                <div class="input-group">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <button class="login-btn" type="submit" name="login">
                    <i class="fas fa-sign-in-alt"></i> Access Admin Dashboard
                </button>
            </form>
            
            <div class="links">
                <a href="index.php">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
                <a href="#">
                    <i class="fas fa-key"></i> Forgot Password?
                </a>
            </div>
            
            <div class="security-badge">
                <i class="fas fa-shield-alt"></i>
                <span>256-bit SSL</span>
                <i class="fas fa-circle" style="font-size: 4px; color: #CBD5E1;"></i>
                <span>2FA Ready</span>
                <i class="fas fa-circle" style="font-size: 4px; color: #CBD5E1;"></i>
                <span>ISO 27001</span>
            </div>
        </div>
    </div>
</div>

<!-- Live Clock -->
<div class="live-clock" id="live-clock">
    <i class="fas fa-clock"></i> Loading...
</div>

<!-- Footer Note -->
<div class="footer-note">
    <i class="fas fa-shield-alt"></i> Protected Area • Authorized Personnel Only
</div>

<script>
function toggleMenu(){
    const menu = document.getElementById("menu");
    menu.classList.toggle('show');
}

// Close menu when clicking outside
document.addEventListener("click", function(e){
    const menu = document.getElementById("menu");
    const dots = document.querySelector(".three-dots");
    
    if(!menu.contains(e.target) && !dots.contains(e.target)){
        menu.classList.remove('show');
    }
});

// Live clock function
function updateClock() {
    const now = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    document.getElementById('live-clock').innerHTML = '<i class="fas fa-clock"></i> ' + time;
}
setInterval(updateClock, 1000);

// Highlight current page in dropdown
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = 'adminlogin.php';
    const menuLinks = document.querySelectorAll('.dropdown a');
    menuLinks.forEach(link => {
        if(link.getAttribute('href') === currentPage) {
            link.style.background = '#3282b8';
            link.style.paddingLeft = '25px';
        }
    });
});
</script>

</body>
</html>
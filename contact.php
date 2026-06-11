<?php
// contact.php - Contact Support Page
session_start();
require_once 'db_config.php';

// FIX: Remove the incorrect redirect - Contact page should be accessible to everyone
// Just check if user is logged in to show user info, but don't redirect

$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? '';
$full_name = $_SESSION['full_name'] ?? '';
$user_email = $_SESSION['email'] ?? '';

// Get unread security alerts count (only if logged in)
$alert_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $alerts_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM security_alerts WHERE user_id = $user_id AND is_read = FALSE");
    if ($alerts_query) {
        $alert_count = mysqli_fetch_assoc($alerts_query)['count'];
    }
}

// Handle contact form submission
$contact_message = '';
if (isset($_POST['send_message'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    
    // OPTION 1: Save to database (recommended)
    $insert_query = "INSERT INTO contact_messages (name, email, subject, message, priority, created_at) 
                     VALUES ('$name', '$email', '$subject', '$message', '$priority', NOW())";
    
    if (mysqli_query($conn, $insert_query)) {
        $contact_message = "<div class='alert success'>
            <i class='fas fa-check-circle'></i> 
            Thank you for contacting us! We'll respond within 24 hours.
        </div>";
        
        // OPTION 2: Also send email (optional)
        // $to = "support@bankifsc.com";
        // $headers = "From: $email\r\n";
        // $headers .= "Reply-To: $email\r\n";
        // mail($to, $subject, $message, $headers);
        
    } else {
        $contact_message = "<div class='alert error'>
            <i class='fas fa-exclamation-circle'></i> 
            Error sending message. Please try again.
        </div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support - Bank IFSC Finder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ALL YOUR EXISTING STYLES REMAIN THE SAME */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Header - Same as banklogin.php */
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
        
        /* User Info Section - Only show if logged in */
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
        
        /* Dropdown Menu */
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
        
        /* Login Prompt for Guests */
        .login-prompt {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            border-left: 4px solid #2563EB;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .login-prompt p {
            color: #1E293B;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .login-prompt i {
            color: #2563EB;
            font-size: 24px;
        }
        
        .login-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-login {
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }
        
        .btn-login.customer {
            background: #2563EB;
            color: white;
        }
        
        .btn-login.bank {
            background: #10B981;
            color: white;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }
        
        /* Rest of your styles remain exactly the same */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 30px;
        }
        
        .info-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .info-card h2 {
            color: #333;
            margin-bottom: 25px;
            font-size: 24px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }
        
        .info-card h2 i {
            color: #2563EB;
            margin-right: 10px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: #F8FAFC;
            border-radius: 15px;
            margin-bottom: 20px;
            transition: 0.3s;
            border: 1px solid #E2E8F0;
        }
        
        .contact-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.1);
            border-color: #2563EB;
        }
        
        .contact-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #2563EB, #1E40AF);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .contact-details h3 {
            color: #333;
            margin-bottom: 8px;
            font-size: 18px;
        }
        
        .contact-details p {
            color: #64748B;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .contact-details .phone-number {
            font-size: 24px;
            font-weight: bold;
            color: #2563EB;
            letter-spacing: 1px;
        }
        
        .contact-details .small-text {
            font-size: 13px;
            color: #94A3B8;
        }
        
        .contact-details a {
            color: #2563EB;
            text-decoration: none;
            font-weight: 600;
        }
        
        .contact-details a:hover {
            text-decoration: underline;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }
        
        .social-link {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            transition: 0.3s;
        }
        
        .social-link:hover {
            transform: translateY(-5px) scale(1.1);
        }
        
        .social-link.whatsapp { background: #25D366; }
        .social-link.facebook { background: #1877F2; }
        .social-link.twitter { background: #1DA1F2; }
        .social-link.instagram { background: #E4405F; }
        .social-link.email { background: #EA4335; }
        
        .office-hours {
            margin-top: 30px;
            background: linear-gradient(135deg, #1E293B, #0F172A);
            color: white;
            padding: 20px;
            border-radius: 15px;
        }
        
        .office-hours h3 {
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .office-hours h3 i {
            color: #2563EB;
            margin-right: 8px;
        }
        
        .hours-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .hours-item:last-child {
            border-bottom: none;
        }
        
        .hours-day {
            font-weight: 600;
        }
        
        .hours-time {
            color: #94A3B8;
        }
        
        .emergency-badge {
            background: #DC2626;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .form-card h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .form-card h2 i {
            color: #2563EB;
            margin-right: 10px;
        }
        
        .form-subtitle {
            color: #64748B;
            margin-bottom: 30px;
            font-size: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-group label i {
            color: #2563EB;
            margin-right: 5px;
            width: 18px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #E2E8F0;
            border-radius: 10px;
            font-size: 15px;
            transition: 0.3s;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #2563EB;
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .priority-select {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .priority-option {
            flex: 1;
            padding: 12px;
            border: 2px solid #E2E8F0;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
        }
        
        .priority-option:hover {
            border-color: #2563EB;
            background: #EFF6FF;
        }
        
        .priority-option.selected {
            border-color: #2563EB;
            background: #2563EB;
            color: white;
        }
        
        .priority-option.low { color: #059669; }
        .priority-option.medium { color: #D97706; }
        .priority-option.high { color: #DC2626; }
        
        .priority-option.selected.low { background: #059669; color: white; }
        .priority-option.selected.medium { background: #D97706; color: white; }
        .priority-option.selected.high { background: #DC2626; color: white; }
        
        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .btn-primary {
            background: #2563EB;
            color: white;
            width: 100%;
        }
        
        .btn-primary:hover {
            background: #1E40AF;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }
        
        .btn-primary i {
            margin-right: 8px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s;
        }
        
        .alert.success {
            background: #DCFCE7;
            color: #059669;
            border-left: 4px solid #059669;
        }
        
        .alert.error {
            background: #FEE2E2;
            color: #DC2626;
            border-left: 4px solid #DC2626;
        }
        
        .alert i {
            font-size: 20px;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .faq-section {
            margin-top: 40px;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .faq-section h2 {
            color: #333;
            margin-bottom: 30px;
            font-size: 24px;
            text-align: center;
        }
        
        .faq-section h2 i {
            color: #2563EB;
            margin-right: 10px;
        }
        
        .faq-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .faq-item {
            background: #F8FAFC;
            padding: 20px;
            border-radius: 15px;
            border-left: 4px solid #2563EB;
        }
        
        .faq-item h4 {
            color: #333;
            margin-bottom: 10px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .faq-item h4 i {
            color: #2563EB;
        }
        
        .faq-item p {
            color: #64748B;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .map-section {
            margin-top: 30px;
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .map-placeholder {
            background: linear-gradient(135deg, #1E293B, #0F172A);
            height: 200px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
            border: 2px dashed #2563EB;
        }
        
        .map-placeholder:hover {
            transform: scale(1.02);
            border-color: #059669;
        }
        
        .map-placeholder i {
            font-size: 30px;
            margin-right: 10px;
            color: #2563EB;
        }
        
        .chat-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #25D366;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(37, 211, 102, 0.3);
            transition: 0.3s;
            z-index: 1000;
        }
        
        .chat-button:hover {
            transform: scale(1.1) rotate(10deg);
        }
        
        .chat-tooltip {
            position: absolute;
            right: 70px;
            background: #333;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            white-space: nowrap;
            opacity: 0;
            transition: 0.3s;
            pointer-events: none;
        }
        
        .chat-button:hover .chat-tooltip {
            opacity: 1;
            right: 80px;
        }
        
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
        
        @media (max-width: 968px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }
            
            .faq-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .user-info {
                margin-left: 0;
                width: 100%;
                justify-content: center;
            }
            
            .dropdown {
                left: 10px;
                right: 10px;
                width: auto;
            }
            
            .login-prompt {
                flex-direction: column;
                text-align: center;
            }
            
            .login-buttons {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header with 3-dot menu -->
    <div class="header">
        <div class="three-dots" onclick="toggleMenu()">
            <i class="fa-solid fa-ellipsis-vertical"></i>
        </div>

        <div class="logo">
            <i class="fa-solid fa-building-columns"></i> BANK <span>IFSC Finder</span>
        </div>

        <!-- User Info Section - Only show if logged in -->
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

        <!-- Dropdown Menu -->
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
    
    <div class="container">
        <!-- Login Prompt for Guests - NEW SECTION -->
        <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="login-prompt">
            <p>
                <i class="fas fa-info-circle"></i>
                You are browsing as a guest. 
                <strong>Login to track your support tickets and get faster responses!</strong>
            </p>
            <div class="login-buttons">
                <a href="customerlogin.php" class="btn-login customer">
                    <i class="fas fa-user"></i> Customer Login
                </a>
                <a href="banklogin.php" class="btn-login bank">
                    <i class="fas fa-university"></i> Bank Login
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Contact Grid -->
        <div class="contact-grid">
            <!-- Contact Information -->
            <div class="info-card">
                <h2><i class="fas fa-address-card"></i> Contact Information</h2>
                
                <!-- Phone - Your Number -->
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Phone Number</h3>
                        <p class="phone-number">+91 74839 81957</p>
                        <p class="small-text">24/7 Emergency Support</p>
                        <a href="tel:+917483981957"><i class="fas fa-arrow-right"></i> Click to call</a>
                    </div>
                </div>
                
                <!-- Email -->
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Email Address</h3>
                        <p>support@bankifsc.com</p>
                        <p>info@bankifsc.com</p>
                        <a href="mailto:support@bankifsc.com">Send email →</a>
                    </div>
                </div>
                
                <!-- WhatsApp - Your Number -->
                <div class="contact-item">
                    <div class="contact-icon" style="background: #25D366;">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="contact-details">
                        <h3>WhatsApp Support</h3>
                        <p class="phone-number">+91 74839 81957</p>
                        <p class="small-text">Quick response on WhatsApp</p>
                        <a href="https://wa.me/917483981957" target="_blank">
                            <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                        </a>
                    </div>
                </div>
                
                <!-- Office Location -->
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-details">
                        <h3>Office Location</h3>
                        <p>Bank IFSC Finder</p>
                        <p>MG Road, Bangalore</p>
                        <p>Karnataka, India - 560001</p>
                    </div>
                </div>
                
                <!-- Social Links -->
                <div class="social-links">
                    <a href="https://wa.me/917483981957" class="social-link whatsapp" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="#" class="social-link facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="mailto:support@bankifsc.com" class="social-link email">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
                
                <!-- Office Hours -->
                <div class="office-hours">
                    <h3><i class="fas fa-clock"></i> Office Hours</h3>
                    <div class="hours-item">
                        <span class="hours-day">Monday - Friday</span>
                        <span class="hours-time">9:00 AM - 6:00 PM</span>
                    </div>
                    <div class="hours-item">
                        <span class="hours-day">Saturday</span>
                        <span class="hours-time">10:00 AM - 4:00 PM</span>
                    </div>
                    <div class="hours-item">
                        <span class="hours-day">Sunday</span>
                        <span class="hours-time">Closed</span>
                    </div>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1);">
                        <span class="emergency-badge">24/7 Emergency</span>
                        <span style="margin-left: 10px; color: #94A3B8;">+91 74839 81957</span>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="form-card">
                <h2><i class="fas fa-paper-plane"></i> Send Message</h2>
                <p class="form-subtitle">Have questions? We'd love to hear from you.</p>
                
                <?php echo $contact_message; ?>
                
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Your Name</label>
                            <input type="text" name="name" value="<?php echo $full_name; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" name="email" value="<?php echo $_SESSION['email'] ?? ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Subject</label>
                        <input type="text" name="subject" placeholder="What is this about?" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-exclamation-triangle"></i> Priority</label>
                        <div class="priority-select" id="prioritySelect">
                            <div class="priority-option low" data-value="low" onclick="selectPriority('low')">
                                <i class="fas fa-chevron-circle-down"></i> Low
                            </div>
                            <div class="priority-option medium selected" data-value="medium" onclick="selectPriority('medium')">
                                <i class="fas fa-minus-circle"></i> Medium
                            </div>
                            <div class="priority-option high" data-value="high" onclick="selectPriority('high')">
                                <i class="fas fa-exclamation-circle"></i> High
                            </div>
                        </div>
                        <input type="hidden" name="priority" id="priorityInput" value="medium">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-comment"></i> Message</label>
                        <textarea name="message" placeholder="Type your message here..." required></textarea>
                    </div>
                    
                    <button type="submit" name="send_message" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
                
                <!-- Response Time -->
                <div style="margin-top: 20px; padding: 15px; background: #EFF6FF; border-radius: 10px; text-align: center; color: #2563EB;">
                    <i class="fas fa-clock"></i> 
                    Average response time: <strong>2-4 hours</strong>
                </div>
            </div>
        </div>
        
        <!-- FAQ Section -->
        <div class="faq-section">
            <h2><i class="fas fa-question-circle"></i> Frequently Asked Questions</h2>
            
            <div class="faq-grid">
                <div class="faq-item">
                    <h4><i class="fas fa-search"></i> How to find IFSC code?</h4>
                    <p>Use our advanced search feature. Select bank, state, and district to get instant IFSC codes for all branches.</p>
                </div>
                
                <div class="faq-item">
                    <h4><i class="fas fa-clock"></i> Support response time?</h4>
                    <p>We typically respond within 2-4 hours during business days. Emergency queries via phone are answered 24/7.</p>
                </div>
                
                <div class="faq-item">
                    <h4><i class="fas fa-shield-alt"></i> Is my data secure?</h4>
                    <p>Yes! We use bank-level security with 2FA, biometric options, and regular security audits to protect your data.</p>
                </div>
                
                <div class="faq-item">
                    <h4><i class="fas fa-phone-alt"></i> Emergency contact?</h4>
                    <p>For urgent issues, call our 24/7 helpline: <strong>+91 74839 81957</strong></p>
                </div>
                
                <div class="faq-item">
                    <h4><i class="fas fa-building"></i> Branch timings?</h4>
                    <p>Most branches operate 10 AM - 4 PM on weekdays. Check specific branch details in our search.</p>
                </div>
                
                <div class="faq-item">
                    <h4><i class="fas fa-download"></i> Can I download IFSC list?</h4>
                    <p>Yes! Use the download feature in bank details page to get PDF or print branch information.</p>
                </div>
            </div>
        </div>
        
        <!-- Map Section -->
        <div class="map-section">
            <div class="map-placeholder" onclick="window.open('https://maps.google.com/?q=Bangalore', '_blank')">
                <i class="fas fa-map-marked-alt"></i>
                <span>Click to view our location on Google Maps</span>
            </div>
        </div>
    </div>
    
    <!-- Live Clock -->
    <div class="live-clock" id="live-clock">
        <i class="fas fa-clock"></i> Loading...
    </div>
    
    <!-- Footer Note -->
    <div class="footer-note">
        <i class="fas fa-shield-alt"></i> Secure Contact Form • End-to-End Encrypted
    </div>
    
    <!-- Live Chat Button -->
    <a href="https://wa.me/917483981957" target="_blank" class="chat-button">
        <i class="fab fa-whatsapp"></i>
        <span class="chat-tooltip">Chat on WhatsApp</span>
    </a>
    
    <script>
        function toggleMenu() {
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
        
        function selectPriority(priority) {
            // Update hidden input
            document.getElementById('priorityInput').value = priority;
            
            // Update UI
            document.querySelectorAll('.priority-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            document.querySelector(`.priority-option.${priority}`).classList.add('selected');
        }
        
        // Live clock function
        function updateClock() {
            const now = new Date();
            const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('live-clock').innerHTML = '<i class="fas fa-clock"></i> ' + time;
        }
        setInterval(updateClock, 1000);
        
        // Highlight current page in dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = 'contact.php';
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
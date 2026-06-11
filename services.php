<?php
session_start();
include "db.php";

// Check if user is logged in
$is_logged_in = isset($_SESSION['accno']) || isset($_SESSION['bname']) || isset($_SESSION['admin']);
$user_name = '';
if (isset($_SESSION['accno'])) {
    $accno = $_SESSION['accno'];
    $result = mysqli_query($conn, "SELECT name FROM account WHERE accno='$accno'");
    if ($row = mysqli_fetch_assoc($result)) {
        $user_name = $row['name'];
    }
} elseif (isset($_SESSION['bname'])) {
    $user_name = $_SESSION['bname'];
} elseif (isset($_SESSION['admin'])) {
    $user_name = 'Administrator';
}

// Fetch current gold rate (simulated - in real app, this would come from an API)
$gold_rate_per_gram = 6250; // ₹ per gram
$silver_rate_per_gram = 75; // ₹ per gram

// Today's date
$today_date = date('d F Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bank Services | Bank IFSC Finder</title>
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
    padding: 16px 24px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    position: relative;
    z-index: 100;
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

/* Back Button */
.back-button {
    display: flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
    box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.3);
}

.back-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(139, 92, 246, 0.4);
}

.back-button i {
    font-size: 14px;
}

/* Welcome Banner (if logged in) */
.welcome-banner {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 12px 25px;
    border-radius: 50px;
    color: #1e293b;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    border: 1px solid rgba(255,255,255,0.2);
    display: inline-flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
}

.welcome-banner i {
    color: #2563eb;
}

.welcome-banner .user-name {
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
}

/* Breadcrumb */
.breadcrumb {
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(255,255,255,0.8);
    font-size: 14px;
}

.breadcrumb a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
}

.breadcrumb a:hover {
    color: white;
}

.breadcrumb i {
    font-size: 12px;
}

/* Main Container */
.main-container {
    max-width: 1300px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

/* Page Header */
.page-header {
    text-align: center;
    margin-bottom: 30px;
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

.page-header h1 {
    font-size: 42px;
    font-weight: 800;
    color: white;
    margin-bottom: 12px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.page-header h1 i {
    background: rgba(255,255,255,0.2);
    padding: 10px;
    border-radius: 50%;
    margin-right: 10px;
}

.page-header p {
    color: rgba(255,255,255,0.9);
    font-size: 18px;
    max-width: 600px;
    margin: 0 auto;
}

/* Date Display */
.date-display {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(5px);
    padding: 10px 20px;
    border-radius: 50px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: white;
    font-weight: 500;
    margin-bottom: 30px;
}

.date-display i {
    color: #ffd700;
}

/* Market Rates Section */
.rates-section {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 25px;
    margin-bottom: 30px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
}

.rates-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    color: #1e293b;
    font-size: 20px;
    font-weight: 700;
}

.rates-title i {
    color: #f59e0b;
    font-size: 24px;
}

.rates-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.rate-card {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    padding: 20px;
    border-radius: 16px;
    text-align: center;
    border-left: 4px solid #f59e0b;
}

.rate-card h4 {
    color: #64748b;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 10px;
}

.rate-value {
    color: #1e293b;
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 5px;
}

.rate-change {
    color: #10b981;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

.rate-change.down {
    color: #ef4444;
}

/* Contact Info Bar */
.contact-bar {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    border-radius: 16px;
    padding: 20px 25px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    color: white;
    box-shadow: 0 10px 15px -3px rgba(37,99,235,0.3);
}

.contact-info {
    display: flex;
    align-items: center;
    gap: 30px;
    flex-wrap: wrap;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.contact-item i {
    font-size: 24px;
    color: #ffd700;
}

.contact-details h4 {
    font-size: 14px;
    font-weight: 400;
    opacity: 0.9;
    margin-bottom: 4px;
}

.contact-details p {
    font-size: 16px;
    font-weight: 600;
}

.contact-details p a {
    color: white;
    text-decoration: none;
}

.contact-details p a:hover {
    text-decoration: underline;
}

.emergency-badge {
    background: rgba(255,255,255,0.2);
    padding: 8px 16px;
    border-radius: 40px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}

.emergency-badge i {
    color: #ffd700;
}

/* Services Grid */
.services-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 25px;
    margin-bottom: 40px;
}

.service-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 30px 25px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.service-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transform: scaleX(0);
    transition: transform 0.4s ease;
}

.service-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 30px 35px -10px rgba(102, 126, 234, 0.4);
}

.service-card:hover::before {
    transform: scaleX(1);
}

.service-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
    font-size: 36px;
    transition: all 0.3s ease;
}

.service-card:hover .service-icon {
    transform: rotate(5deg) scale(1.1);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.service-card h3 {
    color: #1e293b;
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 10px;
}

.service-card p {
    color: #64748b;
    font-size: 13px;
    line-height: 1.5;
    margin-bottom: 15px;
}

.interest-rate {
    background: #f1f5f9;
    padding: 6px 12px;
    border-radius: 30px;
    display: inline-block;
    font-weight: 700;
    color: #2563eb;
    font-size: 14px;
}

.interest-rate i {
    margin-right: 4px;
    color: #f59e0b;
}

/* Special Cards - Different colors for different categories */
.service-card.loan .service-icon {
    background: linear-gradient(135deg, #10b981, #059669);
}
.service-card.loan .interest-rate {
    color: #059669;
}

.service-card.deposit .service-icon {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}
.service-card.deposit .interest-rate {
    color: #d97706;
}

.service-card.card .service-icon {
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
}
.service-card.card .interest-rate {
    color: #6d28d9;
}

.service-card.investment .service-icon {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}
.service-card.investment .interest-rate {
    color: #2563eb;
}

/* EMI Calculator Link */
.calculator-link {
    text-align: center;
    margin: 30px 0;
}

.calculator-btn {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    text-decoration: none;
    padding: 15px 35px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
    box-shadow: 0 10px 15px -3px rgba(245,158,11,0.3);
}

.calculator-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 25px -5px rgba(245,158,11,0.4);
}

/* Feature Banner */
.feature-banner {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 24px;
    padding: 40px;
    margin-top: 40px;
    text-align: center;
    color: white;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
}

.feature-banner h2 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 15px;
}

.feature-banner p {
    font-size: 16px;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto 25px;
}

.feature-banner .btn {
    background: white;
    color: #1e293b;
    padding: 14px 35px;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.feature-banner .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
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
    color: #667eea;
    margin-right: 5px;
}

/* Responsive */
@media (max-width: 1024px) {
    .rates-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .services-grid {
        grid-template-columns: repeat(3, 1fr);
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
    
    .rates-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .services-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .contact-bar {
        flex-direction: column;
        text-align: center;
    }
    
    .contact-info {
        justify-content: center;
    }
    
    .page-header h1 {
        font-size: 32px;
    }
    
    .page-header p {
        font-size: 16px;
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
    .rates-grid {
        grid-template-columns: 1fr;
    }
    
    .services-grid {
        grid-template-columns: 1fr;
    }
    
    .contact-info {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .feature-banner {
        padding: 30px 20px;
    }
    
    .feature-banner h2 {
        font-size: 24px;
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
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 10px;
}
</style>
</head>
<body>

<!-- Background Pattern -->
<div class="background-pattern"></div>

<!-- Header -->
<div class="header">
    <div class="logo">
        <i class="fas fa-building-columns"></i> BANK <span>IFSC</span>
    </div>
    
    <!-- Back to Profile Button -->
    <a href="customer_profile.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Profile
    </a>
</div>

<!-- Welcome Banner (if logged in) -->
<?php if ($is_logged_in): ?>
<div class="welcome-banner">
    <i class="fas fa-user-circle"></i>
    <span>Welcome,</span>
    <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
</div>
<?php endif; ?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="index.php">Home</a>
    <i class="fas fa-chevron-right"></i>
    <a href="customer_profile.php">Profile</a>
    <i class="fas fa-chevron-right"></i>
    <span>Services</span>
</div>

<!-- Main Container -->
<div class="main-container">
    
    <!-- Date Display -->
    <div class="date-display">
        <i class="fas fa-calendar-alt"></i>
        <span>Today: <?php echo $today_date; ?></span>
    </div>
    
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fa fa-hand-holding-heart"></i>
            Bank Services
        </h1>
        <p>Explore our comprehensive range of banking products and services</p>
    </div>

    <!-- Contact Information Bar -->
    <div class="contact-bar">
        <div class="contact-info">
            <div class="contact-item">
                <i class="fas fa-user-tie"></i>
                <div class="contact-details">
                    <h4>Relationship Manager</h4>
                    <p>Praveen B N</p>
                </div>
            </div>
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <div class="contact-details">
                    <h4>Email</h4>
                    <p><a href="mailto:praveen825kcgmail.com">praveen825kcgmail.com</a></p>
                </div>
            </div>
            <div class="contact-item">
                <i class="fas fa-phone-alt"></i>
                <div class="contact-details">
                    <h4>Toll Free</h4>
                    <p>1800-123-4567</p>
                </div>
            </div>
        </div>
        <div class="emergency-badge">
            <i class="fas fa-shield-alt"></i>
            <span>24/7 Support</span>
        </div>
    </div>

    <!-- Live Market Rates Section -->
    <div class="rates-section">
        <div class="rates-title">
            <i class="fas fa-chart-line"></i>
            <span>Today's Live Market Rates</span>
        </div>
        <div class="rates-grid">
            <div class="rate-card">
                <h4>Gold (24K) / 10g</h4>
                <div class="rate-value">₹<?php echo number_format($gold_rate_per_gram * 10); ?></div>
                <div class="rate-change">
                    <i class="fas fa-arrow-up"></i> +0.8%
                </div>
            </div>
            <div class="rate-card">
                <h4>Silver / Kg</h4>
                <div class="rate-value">₹<?php echo number_format($silver_rate_per_gram * 1000); ?></div>
                <div class="rate-change">
                    <i class="fas fa-arrow-down"></i> -0.3%
                </div>
            </div>
            <div class="rate-card">
                <h4>FD Interest Rate</h4>
                <div class="rate-value">7.2% - 8.5%</div>
                <div class="rate-change">
                    <i class="fas fa-arrow-up"></i> +0.25%
                </div>
            </div>
            <div class="rate-card">
                <h4>Repo Rate</h4>
                <div class="rate-value">6.50%</div>
                <div class="rate-change">
                    <i class="fas fa-minus"></i> Unchanged
                </div>
            </div>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="services-grid">
        
        <!-- Gold Loans -->
        <div class="service-card loan">
            <div class="service-icon">
                <i class="fa fa-coins"></i>
            </div>
            <h3>Gold Loans</h3>
            <p>Instant loans against gold with minimal interest rates</p>
            <span class="interest-rate"><i class="fas fa-percent"></i> 7.5% - 9.5%</span>
        </div>

        <!-- Recurring Deposit (RD) -->
        <div class="service-card deposit">
            <div class="service-icon">
                <i class="fa fa-piggy-bank"></i>
            </div>
            <h3>Recurring Deposit</h3>
            <p>Build savings with monthly deposits</p>
            <span class="interest-rate"><i class="fas fa-percent"></i> 7.2% p.a.</span>
        </div>

        <!-- Fixed Deposit (FD) -->
        <div class="service-card deposit">
            <div class="service-icon">
                <i class="fa fa-university"></i>
            </div>
            <h3>Fixed Deposit</h3>
            <p>Secure higher returns with flexible tenures</p>
            <span class="interest-rate"><i class="fas fa-percent"></i> 7.5% - 8.5%</span>
        </div>

        <!-- Credit Cards -->
        <div class="service-card card">
            <div class="service-icon">
                <i class="fa fa-credit-card"></i>
            </div>
            <h3>Credit Cards</h3>
            <p>Premium cards with exclusive rewards</p>
            <span class="interest-rate"><i class="fas fa-rupee-sign"></i> ₹499/yr</span>
        </div>

        <!-- Home Loans -->
        <div class="service-card loan">
            <div class="service-icon">
                <i class="fa fa-home"></i>
            </div>
            <h3>Home Loans</h3>
            <p>Make your dream home a reality</p>
            <span class="interest-rate"><i class="fas fa-percent"></i> 8.5% - 9.5%</span>
        </div>

        <!-- Vehicle Loans -->
        <div class="service-card loan">
            <div class="service-icon">
                <i class="fa fa-car"></i>
            </div>
            <h3>Vehicle Loans</h3>
            <p>Drive your dream car today</p>
            <span class="interest-rate"><i class="fas fa-percent"></i> 8.0% - 9.0%</span>
        </div>

        <!-- Personal Loans -->
        <div class="service-card loan">
            <div class="service-icon">
                <i class="fa fa-briefcase"></i>
            </div>
            <h3>Personal Loans</h3>
            <p>Unsecured loans for your needs</p>
            <span class="interest-rate"><i class="fas fa-percent"></i> 10.5% - 12.5%</span>
        </div>

        <!-- Investment & Mutual Funds -->
        <div class="service-card investment">
            <div class="service-icon">
                <i class="fa fa-chart-line"></i>
            </div>
            <h3>Mutual Funds</h3>
            <p>Grow your wealth with expert guidance</p>
            <span class="interest-rate"><i class="fas fa-chart-pie"></i> 12-18% returns</span>
        </div>
    </div>

    <!-- EMI Calculator Link -->
    <div class="calculator-link">
        <a href="#" class="calculator-btn">
            <i class="fas fa-calculator"></i> Calculate Your EMI
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <!-- Feature Banner -->
    <div class="feature-banner">
        <h2>Need Personalized Assistance?</h2>
        <p>Our dedicated relationship manager Praveen B N is here to help you choose the right financial product</p>
        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="tel:18001234567" class="btn">
                <i class="fa fa-phone-alt"></i> Call Now
            </a>
            <a href="mailto:praveen825kcgmail.com" class="btn" style="background: transparent; border: 2px solid white; color: white;">
                <i class="fa fa-envelope"></i> Email Us
            </a>
        </div>
    </div>
</div>

<!-- Live Date -->
<div class="live-date" id="live-date">
    <i class="fas fa-calendar-alt"></i> Loading...
</div>

<!-- Footer Note -->
<div class="footer-note">
    <i class="fas fa-shield-alt"></i> Trusted Banking • Your partner in progress
</div>

<script>
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
</script>

</body>
</html>
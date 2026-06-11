<?php
// about.php - Enhanced with 3-dot menu and username integration
session_start();
require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login1.php");
    exit();
}

// Get logged-in user info
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];

// Get unread security alerts count
$alerts_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM security_alerts WHERE user_id = $user_id AND is_read = FALSE");
$alert_count = 0;
if ($alerts_query) {
    $alert_count = mysqli_fetch_assoc($alerts_query)['count'];
}

// Get some stats for the about page
$totalCustomers = 15000000; // 15M+
$totalBranches = 850;
$totalEmployees = 12500;
$totalYears = 39;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - BANK IFSC Finder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #F5F7FB;
            padding: 15px;
            height: 100vh;
            overflow: hidden;
        }

        /* Header with 3-dot menu - EXACT same as index.php */
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

        /* User Info Section - EXACT same as index.php */
        .user-info {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: flex-end;
            max-width: 70%;
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

        /* Dropdown Menu - EXACT same as index.php */
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

        /* Welcome Section */
        .welcome-section {
            margin-bottom: 20px;
            padding: 0 5px;
        }
        
        .welcome-section h1 {
            color: #1E293B;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .welcome-section h1 span {
            color: #2563EB;
        }
        
        .welcome-section p {
            color: #64748B;
            font-size: 16px;
        }

        /* About Container */
        .about-container {
            max-width: 1400px;
            margin: 0 auto;
            height: calc(100vh - 150px);
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .about-container::-webkit-scrollbar {
            width: 5px;
        }
        .about-container::-webkit-scrollbar-thumb {
            background: #2563EB;
            border-radius: 5px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #E9ECF2;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: #EFF6FF;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563EB;
            font-size: 24px;
        }

        .stat-content h3 {
            color: #64748B;
            font-size: 14px;
            font-weight: 400;
            margin-bottom: 5px;
        }

        .stat-content .number {
            color: #1E293B;
            font-size: 28px;
            font-weight: 700;
        }

        /* Main Grid - 3 Columns */
        .main-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 16px;
            border: 1px solid #E9ECF2;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #0A2472 0%, #2563EB 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header i {
            font-size: 24px;
            color: #E6B422;
        }

        .card-header h2 {
            font-size: 18px;
            font-weight: 600;
        }

        .card-body {
            padding: 20px;
        }

        /* Company Overview Stats */
        .overview-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .overview-item {
            text-align: center;
            padding: 15px;
            background: #F8FAFC;
            border-radius: 12px;
        }

        .overview-number {
            font-size: 32px;
            font-weight: 700;
            color: #2563EB;
            margin-bottom: 5px;
        }

        .overview-label {
            color: #64748B;
            font-size: 13px;
        }

        /* Timeline */
        .timeline {
            margin-top: 15px;
        }

        .timeline-item {
            display: flex;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #EDF2F7;
        }

        .timeline-year {
            font-weight: 700;
            color: #2563EB;
            min-width: 60px;
        }

        .timeline-desc {
            color: #1E293B;
            font-size: 14px;
        }

        /* Badges */
        .badge-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .badge {
            background: #EFF6FF;
            color: #2563EB;
            padding: 8px 15px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #2563EB;
        }

        /* Leadership */
        .leader-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px;
            background: #F8FAFC;
            border-radius: 12px;
            margin-bottom: 10px;
        }

        .leader-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #2563EB 0%, #1d4ed8 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .leader-info h4 {
            color: #1E293B;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .leader-info p {
            color: #64748B;
            font-size: 13px;
        }

        /* Values Grid */
        .values-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 15px 0;
        }

        .value-item {
            text-align: center;
            padding: 15px;
            background: #F8FAFC;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .value-item:hover {
            background: #2563EB;
            color: white;
        }

        .value-item:hover i,
        .value-item:hover span {
            color: white;
        }

        .value-item i {
            color: #2563EB;
            font-size: 24px;
            margin-bottom: 8px;
        }

        .value-item span {
            color: #1E293B;
            font-size: 13px;
            font-weight: 600;
            display: block;
        }

        /* Awards */
        .award-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #F8FAFC;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: all 0.3s;
        }

        .award-item:hover {
            background: #EFF6FF;
            transform: translateX(5px);
        }

        .award-icon {
            width: 40px;
            height: 40px;
            background: #FFD700;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1E293B;
            font-size: 20px;
        }

        .award-content h4 {
            color: #1E293B;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .award-content p {
            color: #64748B;
            font-size: 12px;
        }

        /* Contact Info */
        .contact-info {
            background: #F8FAFC;
            padding: 15px;
            border-radius: 12px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #E2E8F0;
        }

        .contact-item:last-child {
            border-bottom: none;
        }

        .contact-item i {
            color: #2563EB;
            width: 20px;
        }

        .contact-item span {
            color: #1E293B;
            font-size: 14px;
        }

        /* Global Presence */
        .country-badge {
            background: #2563EB;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin: 3px;
        }

        /* Mission Box */
        .mission-box {
            background: linear-gradient(135deg, #2563EB 0%, #1d4ed8 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }

        .mission-box p {
            font-size: 14px;
            line-height: 1.6;
            opacity: 0.9;
        }

        /* Footer Note */
        .footer-note {
            background: white;
            padding: 12px 20px;
            border-radius: 10px;
            border: 1px solid #E9ECF2;
            text-align: center;
            color: #64748B;
            font-size: 12px;
        }

        /* Live Clock - Same as index.php */
        .live-clock {
            position: fixed;
            bottom: 25px;
            right: 35px;
            background: white;
            padding: 8px 15px;
            border-radius: 30px;
            border: 1px solid #E9ECF2;
            font-weight: 600;
            color: #2563EB;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            font-size: 14px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(37, 99, 235, 0.2);
        }

        .live-clock:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(37, 99, 235, 0.2);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .user-info {
                max-width: 50%;
            }
        }
    </style>
</head>
<body>

<!-- Header with 3-dot menu - EXACT copy from index.php -->
<div class="header">
    <div class="three-dots" onclick="toggleMenu()">
        <i class="fa-solid fa-ellipsis-vertical"></i>
    </div>

    <div class="logo">
        <i class="fa-solid fa-building-columns"></i> BANK <span>IFSC Finder</span>
    </div>

    <!-- User Info Section -->
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

    <!-- Dropdown Menu - EXACT copy from index.php with all features -->
    <div class="dropdown" id="menu">
        <div class="dropdown-user">
            <div class="user-name"><?php echo htmlspecialchars($full_name ?: $username); ?></div>
            <div class="user-email">Logged in</div>
        </div>
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
        <a href="logout1.php" style="background:#DC2626;"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<!-- Welcome Section with Username -->
<div class="welcome-section">
    <h1>About <span>Us</span></h1>
    <p>Welcome, <?php echo htmlspecialchars($full_name ?: $username); ?>! Learn more about our bank's journey and values</p>
</div>

<!-- About Container -->
<div class="about-container">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-content">
                <h3>Years of Trust</h3>
                <div class="number"><?php echo $totalYears; ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <h3>Happy Customers</h3>
                <div class="number">15M+</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-building"></i></div>
            <div class="stat-content">
                <h3>Branches</h3>
                <div class="number"><?php echo $totalBranches; ?>+</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-trophy"></i></div>
            <div class="stat-content">
                <h3>Awards Won</h3>
                <div class="number">45+</div>
            </div>
        </div>
    </div>

    <!-- Main Grid - 3 Columns -->
    <div class="main-grid">
        <!-- Column 1: Company Overview -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-building"></i>
                <h2>Company Overview</h2>
            </div>
            <div class="card-body">
                <div class="overview-stats">
                    <div class="overview-item">
                        <div class="overview-number"><?php echo $totalYears; ?></div>
                        <div class="overview-label">Years Legacy</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-number">850+</div>
                        <div class="overview-label">Branches</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-number">15M+</div>
                        <div class="overview-label">Customers</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-number">45+</div>
                        <div class="overview-label">Countries</div>
                    </div>
                </div>

                <div class="timeline">
                    <div class="timeline-item">
                        <span class="timeline-year">1985</span>
                        <span class="timeline-desc">Founded in Mumbai with 5 branches</span>
                    </div>
                    <div class="timeline-item">
                        <span class="timeline-year">2000</span>
                        <span class="timeline-desc">Expanded to 100+ branches</span>
                    </div>
                    <div class="timeline-item">
                        <span class="timeline-year">2010</span>
                        <span class="timeline-desc">Digital banking transformation</span>
                    </div>
                    <div class="timeline-item">
                        <span class="timeline-year">2020</span>
                        <span class="timeline-desc">Reached 10M+ customers</span>
                    </div>
                    <div class="timeline-item">
                        <span class="timeline-year">2024</span>
                        <span class="timeline-desc">Most trusted bank award</span>
                    </div>
                </div>

                <div class="badge-container">
                    <span class="badge"><i class="fas fa-check-circle"></i> ISO 27001</span>
                    <span class="badge"><i class="fas fa-shield-alt"></i> PCI DSS</span>
                    <span class="badge"><i class="fas fa-star"></i> CRISIL 5-Star</span>
                </div>
            </div>
        </div>

        <!-- Column 2: Leadership & Values -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-users"></i>
                <h2>Leadership & Values</h2>
            </div>
            <div class="card-body">
                <div class="leader-item">
                    <div class="leader-avatar"><i class="fas fa-user-tie"></i></div>
                    <div class="leader-info">
                        <h4>Rajesh Mehta</h4>
                        <p>Chairman & MD • 35 years experience</p>
                    </div>
                </div>
                <div class="leader-item">
                    <div class="leader-avatar"><i class="fas fa-user-tie"></i></div>
                    <div class="leader-info">
                        <h4>Priya Singh</h4>
                        <p>CEO • Harvard Business School</p>
                    </div>
                </div>
                <div class="leader-item">
                    <div class="leader-avatar"><i class="fas fa-user-tie"></i></div>
                    <div class="leader-info">
                        <h4>Amit Kumar</h4>
                        <p>CFO • 20 years financial expertise</p>
                    </div>
                </div>
                <div class="leader-item">
                    <div class="leader-avatar"><i class="fas fa-user-tie"></i></div>
                    <div class="leader-info">
                        <h4>Neha Gupta</h4>
                        <p>CTO • Digital transformation lead</p>
                    </div>
                </div>

                <h4 style="color: #1E293B; margin: 15px 0 10px;">Core Values</h4>
                <div class="values-grid">
                    <div class="value-item">
                        <i class="fas fa-handshake"></i>
                        <span>Integrity</span>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-heart"></i>
                        <span>Customer First</span>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-lightbulb"></i>
                        <span>Innovation</span>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-star"></i>
                        <span>Excellence</span>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-shield"></i>
                        <span>Security</span>
                    </div>
                    <div class="value-item">
                        <i class="fas fa-hand-holding-heart"></i>
                        <span>Trust</span>
                    </div>
                </div>

                <div class="mission-box">
                    <i class="fas fa-quote-left" style="margin-right: 5px;"></i>
                    <p>Empowering financial growth with trust, innovation, and unwavering commitment to customer success.</p>
                </div>
            </div>
        </div>

        <!-- Column 3: Recognition & Contact -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-award"></i>
                <h2>Recognition & Contact</h2>
            </div>
            <div class="card-body">
                <div class="award-item">
                    <div class="award-icon"><i class="fas fa-trophy"></i></div>
                    <div class="award-content">
                        <h4>Best Digital Bank 2023</h4>
                        <p>Banking Technology Awards</p>
                    </div>
                </div>
                <div class="award-item">
                    <div class="award-icon"><i class="fas fa-medal"></i></div>
                    <div class="award-content">
                        <h4>Most Trusted Bank 2023</h4>
                        <p>Economic Times</p>
                    </div>
                </div>
                <div class="award-item">
                    <div class="award-icon"><i class="fas fa-star"></i></div>
                    <div class="award-content">
                        <h4>Excellence in Customer Service</h4>
                        <p>Forbes India</p>
                    </div>
                </div>
                <div class="award-item">
                    <div class="award-icon"><i class="fas fa-globe"></i></div>
                    <div class="award-content">
                        <h4>Best Mobile Banking App</h4>
                        <p>Global Banking Summit</p>
                    </div>
                </div>

                <h4 style="color: #1E293B; margin: 15px 0 10px;">Global Presence</h4>
        <div style="display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 15px;">
                    <span class="country-badge"><i class="fas fa-map-marker-alt"></i> India</span>
                    <span class="country-badge"><i class="fas fa-map-marker-alt"></i> UAE</span>
                    <span class="country-badge"><i class="fas fa-map-marker-alt"></i> UK</span>
                    <span class="country-badge"><i class="fas fa-map-marker-alt"></i> USA</span>
                    <span class="country-badge"><i class="fas fa-map-marker-alt"></i> Singapore</span>
                    <span class="country-badge"><i class="fas fa-map-marker-alt"></i> Canada</span>
                </div>

                <h4 style="color: #1E293B; margin: 15px 0 10px;">Quick Contact</h4>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>BKC, Bandra East, Mumbai - 400051</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>1800 123 4567 (24/7 Toll Free)</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>corporate@bankifscfinder.com</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <span>Mon-Fri: 9:00 AM - 5:00 PM</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-globe"></i>
                        <span>www.bankifscfinder.com</span>
                    </div>
                </div>

                <div style="margin-top: 15px; font-size: 11px; color: #94A3B8; text-align: center;">
                    CIN: L65110MH1985PLC123456 | RBI License No. 12345<br>
                    Member DICGC | ISO 27001:2022 Certified
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="footer-note">
        <i class="fas fa-shield-alt" style="color: #2563EB; margin-right: 5px;"></i>
        Trusted by over 15 million customers • 39 years of excellence • 850+ branches worldwide
    </div>
</div>

<!-- Live Clock - Same as index.php -->
<div class="live-clock" id="live-clock">
    <i class="fas fa-clock"></i> Loading...
</div>

<!-- JavaScript - EXACT same functions from index.php -->
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

// Live clock
function updateClock() {
    const now = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    document.getElementById('live-clock').innerHTML = '<i class="fas fa-clock"></i> ' + time;
}
setInterval(updateClock, 1000);

// Highlight current page in dropdown
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = 'about.php';
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
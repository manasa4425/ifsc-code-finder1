<?php
session_start();
include "db.php";

// Check if bank is logged in
if (!isset($_SESSION['bname'])) {
    header("Location: banklogin.php");
    exit();
}

$bank_name = $_SESSION['bname'] ?? 'Bank Name';
$bank_ifsc = $_SESSION['ifsc'] ?? 'IFSC Code';

// Get all banks for dropdown
$banks_query = mysqli_query($conn, "SELECT DISTINCT bank_name FROM branches ORDER BY bank_name");
$banks_list = [];
while($row = mysqli_fetch_assoc($banks_query)) {
    $banks_list[] = $row['bank_name'];
}

// Search results
$search_results = [];
$search_message = '';

if(isset($_POST['search'])) {
    $bank = mysqli_real_escape_string($conn, $_POST['bank']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);
    
    // Build query based on selections
    $conditions = [];
    if(!empty($bank)) $conditions[] = "bank_name = '$bank'";
    if(!empty($state)) $conditions[] = "state = '$state'";
    if(!empty($district)) $conditions[] = "district = '$district'";
    
    if(count($conditions) > 0) {
        $where = implode(" AND ", $conditions);
        $query = "SELECT * FROM branches WHERE $where ORDER BY bank_name, state, district, city";
        $result = mysqli_query($conn, $query);
        
        if($result && mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $search_results[] = $row;
            }
            $search_message = "<div class='alert success'>✅ Found " . count($search_results) . " branch(es)</div>";
        } else {
            $search_message = "<div class='alert error'>❌ No branches found matching your criteria</div>";
        }
    } else {
        $search_message = "<div class='alert error'>⚠️ Please select at least one option</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Search IFSC Code | Bank IFSC Finder</title>
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

/* Welcome Banner */
.welcome-banner {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 12px 30px;
    border-radius: 50px;
    color: #1E293B;
    font-size: 16px;
    font-weight: 600;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    border: 1px solid rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    gap: 15px;
    animation: slideDown 0.6s ease;
    z-index: 100;
    white-space: nowrap;
}

.welcome-banner i {
    color: #667eea;
    font-size: 20px;
}

.welcome-banner .bank-name {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
}

.status-container {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(16, 185, 129, 0.1);
    padding: 5px 12px;
    border-radius: 30px;
    border-left: 2px solid #10B981;
}

.status-dot {
    width: 8px;
    height: 8px;
    background: #10B981;
    border-radius: 50%;
    box-shadow: 0 0 10px #10B981;
    animation: pulse 2s infinite;
}

.status-text {
    color: #10B981;
    font-weight: 600;
    font-size: 13px;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.1); }
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translate(-50%, -20px);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0);
    }
}

/* Main Container */
.main-container {
    width: 100%;
    max-width: 1400px;
    margin: 80px auto 20px;
    position: relative;
    z-index: 1;
    animation: fadeInUp 0.8s ease;
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

/* Navigation Tabs */
.nav-tabs {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.tab-link {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    text-decoration: none;
    padding: 12px 20px;
    border-radius: 40px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(5px);
}

.tab-link i {
    font-size: 16px;
}

.tab-link:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-3px);
    border-color: #ffd700;
}

.tab-link.active {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    box-shadow: 0 10px 25px rgba(245, 158, 11, 0.4);
    border-color: transparent;
}

.tab-link.logout {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.3);
}

.tab-link.logout:hover {
    background: rgba(239, 68, 68, 0.4);
    border-color: #ef4444;
}

/* Content Card */
.content-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 30px;
    padding: 35px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Bank Info Bar */
.bank-info-bar {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.bank-info-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.bank-info-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.bank-info-text h4 {
    color: #64748B;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 3px;
}

.bank-info-text span {
    color: #1E293B;
    font-size: 16px;
    font-weight: 700;
}

/* Page Header */
.page-header {
    text-align: center;
    margin-bottom: 30px;
}

.page-header h2 {
    color: #1E293B;
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.page-header h2 i {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 36px;
}

.page-header p {
    color: #64748B;
    font-size: 15px;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    color: white;
    font-size: 28px;
}

.stat-number {
    font-size: 32px;
    font-weight: 700;
    color: #1E293B;
}

.stat-label {
    color: #64748B;
    font-size: 14px;
    margin-top: 5px;
}

/* Search Card */
.search-card {
    background: white;
    border-radius: 24px;
    padding: 35px;
    margin-bottom: 30px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
}

.search-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 25px;
    color: #1E293B;
    font-size: 20px;
    font-weight: 600;
}

.search-title i {
    color: #f59e0b;
    font-size: 24px;
}

.search-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    margin-bottom: 30px;
}

.input-group {
    position: relative;
}

.input-group label {
    display: block;
    margin-bottom: 10px;
    color: #1E293B;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.input-group label i {
    color: #f59e0b;
    margin-right: 8px;
    font-size: 14px;
}

.input-wrapper {
    position: relative;
}

.input-wrapper i {
    position: absolute;
    top: 50%;
    left: 16px;
    transform: translateY(-50%);
    color: #f59e0b;
    font-size: 16px;
    z-index: 1;
}

select {
    width: 100%;
    padding: 14px 14px 14px 45px;
    border: 2px solid #E2E8F0;
    border-radius: 16px;
    outline: none;
    background: white;
    color: #1E293B;
    font-size: 15px;
    transition: all 0.3s;
    appearance: none;
    cursor: pointer;
}

select:focus {
    border-color: #f59e0b;
    box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
}

select:disabled {
    background: #F8FAFC;
    color: #94A3B8;
    cursor: not-allowed;
    border-color: #E2E8F0;
}

.select-arrow {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #f59e0b;
    font-size: 14px;
    pointer-events: none;
}

/* Button Group */
.button-group {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.btn {
    padding: 16px 35px;
    border: none;
    border-radius: 50px;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn-search {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3);
}

.btn-search:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(245, 158, 11, 0.4);
}

.btn-reset {
    background: #F1F5F9;
    color: #64748B;
    border: 2px solid #E2E8F0;
}

.btn-reset:hover {
    background: #E2E8F0;
    transform: translateY(-3px);
    border-color: #f59e0b;
}

/* Selection Badges */
.selection-badges {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 25px;
    flex-wrap: wrap;
}

.selection-badge {
    background: #FEF3C7;
    color: #92400E;
    padding: 8px 20px;
    border-radius: 40px;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    border-left: 4px solid #F59E0B;
}

.selection-badge i {
    color: #F59E0B;
}

/* Results Card */
.results-card {
    background: white;
    border-radius: 24px;
    padding: 30px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.results-header h3 {
    color: #1E293B;
    font-size: 20px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.results-header h3 i {
    color: #f59e0b;
}

.result-count {
    background: #F1F5F9;
    padding: 8px 20px;
    border-radius: 40px;
    color: #1E293B;
    font-size: 14px;
    font-weight: 600;
}

.result-count i {
    color: #f59e0b;
    margin-right: 5px;
}

/* Table */
.table-container {
    overflow-x: auto;
    border-radius: 16px;
    border: 1px solid #E2E8F0;
    margin-top: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    font-weight: 600;
    font-size: 14px;
    text-align: left;
}

td {
    padding: 15px;
    border-bottom: 1px solid #E2E8F0;
    color: #1E293B;
    font-size: 14px;
}

tr:hover {
    background: #F8FAFC;
}

/* Bank Badge */
.bank-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

/* IFSC Code */
.ifsc-code {
    font-family: monospace;
    font-weight: 700;
    color: #f59e0b;
    font-size: 14px;
    background: #FEF3C7;
    padding: 6px 12px;
    border-radius: 8px;
    display: inline-block;
    letter-spacing: 1px;
}

/* Copy Button */
.copy-btn {
    background: none;
    border: 1px solid #667eea;
    color: #667eea;
    padding: 6px 15px;
    border-radius: 30px;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.copy-btn:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
    transform: translateY(-2px);
}

/* Alert Messages */
.alert {
    padding: 15px 20px;
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
    background: #ECFDF5;
    color: #059669;
    border-left: 4px solid #10B981;
}

.alert.error {
    background: #FEF2F2;
    color: #DC2626;
    border-left: 4px solid #EF4444;
}

.alert i {
    font-size: 18px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #64748B;
}

.empty-state i {
    font-size: 60px;
    color: #CBD5E1;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #1E293B;
    font-size: 20px;
    margin-bottom: 10px;
}

.empty-state p {
    font-size: 14px;
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
    color: #1E293B;
    font-size: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    z-index: 100;
}

.footer-note i {
    color: #10B981;
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
    color: #1E293B;
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
@media (max-width: 768px) {
    body {
        padding: 15px;
    }
    
    .main-container {
        margin: 100px auto 20px;
    }
    
    .welcome-banner {
        width: 95%;
        font-size: 13px;
        padding: 10px 15px;
        white-space: normal;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .search-grid {
        grid-template-columns: 1fr;
    }
    
    .content-card, .search-card, .results-card {
        padding: 20px;
    }
    
    .bank-info-bar {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .button-group {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
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

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #F1F5F9;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}
</style>
</head>
<body>

<!-- Background Pattern -->
<div class="background-pattern"></div>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <i class="fas fa-university"></i>
    <span>Welcome,</span>
    <span class="bank-name"><?php echo htmlspecialchars($bank_name); ?></span>
    
    <div class="status-container">
        <span class="status-dot"></span>
        <span class="status-text">BANK ACCESS</span>
    </div>
</div>

<!-- Main Container -->
<div class="main-container">
    
    <!-- Navigation Tabs -->
    <div class="nav-tabs">
        <a href="addaccount.php" class="tab-link">
            <i class="fa fa-user-plus"></i> Add Account
        </a>
        <a href="Viewaccount.php" class="tab-link">
            <i class="fa fa-eye"></i> View Accounts
        </a>
        <a href="deposite.php" class="tab-link">
            <i class="fa fa-money-bill"></i> Deposit
        </a>
        <a href="withdraw.php" class="tab-link">
            <i class="fa fa-hand-holding-dollar"></i> Withdraw
        </a>
        <a href="transaction.php" class="tab-link">
            <i class="fa fa-right-left"></i> Transaction
        </a>
        <a href="searchifsc.php" class="tab-link active">
            <i class="fa fa-magnifying-glass"></i> Search IFSC
        </a>
        <a href="alldeatils.php" class="tab-link">
            <i class="fa fa-history"></i> All Details
        </a>
        <a href="logout.php" class="tab-link logout">
            <i class="fa fa-sign-out-alt"></i> Logout
        </a>
    </div>
    
    <!-- Content Card -->
    <div class="content-card">
        
        <!-- Bank Info Bar -->
        <div class="bank-info-bar">
            <div class="bank-info-item">
                <div class="bank-info-icon">
                    <i class="fa fa-university"></i>
                </div>
                <div class="bank-info-text">
                    <h4>BANK NAME</h4>
                    <span><?php echo htmlspecialchars($bank_name); ?></span>
                </div>
            </div>
            
            <div class="bank-info-item">
                <div class="bank-info-icon">
                    <i class="fa fa-qrcode"></i>
                </div>
                <div class="bank-info-text">
                    <h4>IFSC CODE</h4>
                    <span><?php echo htmlspecialchars($bank_ifsc); ?></span>
                </div>
            </div>
            
            <div class="bank-info-item">
                <div class="bank-info-icon">
                    <i class="fa fa-calendar"></i>
                </div>
                <div class="bank-info-text">
                    <h4>DATE</h4>
                    <span><?php echo date('d M Y'); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Page Header -->
        <div class="page-header">
            <h2>
                <i class="fa fa-magnifying-glass"></i>
                Search IFSC Code
            </h2>
            <p>Find branch details and IFSC codes by Bank, State & District</p>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <?php
            $total_banks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT bank_name) as count FROM branches"))['count'];
            $total_states = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT state) as count FROM branches"))['count'];
            $total_branches = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM branches"))['count'];
            $total_districts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT district) as count FROM branches"))['count'];
            ?>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa fa-university"></i>
                </div>
                <div class="stat-number"><?php echo $total_banks; ?></div>
                <div class="stat-label">Total Banks</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa fa-map-marker-alt"></i>
                </div>
                <div class="stat-number"><?php echo $total_states; ?></div>
                <div class="stat-label">States</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa fa-code-branch"></i>
                </div>
                <div class="stat-number"><?php echo $total_branches; ?></div>
                <div class="stat-label">Branches</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa fa-city"></i>
                </div>
                <div class="stat-number"><?php echo $total_districts; ?></div>
                <div class="stat-label">Districts</div>
            </div>
        </div>
        
        <!-- Search Card -->
        <div class="search-card">
            <div class="search-title">
                <i class="fa fa-filter"></i>
                <span>Filter Branches by Location</span>
            </div>
            
            <form method="POST" id="searchForm">
                <div class="search-grid">
                    <div class="input-group">
                        <label><i class="fa fa-university"></i> Select Bank</label>
                        <div class="input-wrapper">
                            <i class="fa fa-building"></i>
                            <select name="bank" id="bankSelect" required onchange="loadStates()">
                                <option value="">-- Choose Bank --</option>
                                <?php foreach($banks_list as $bank): ?>
                                <option value="<?php echo $bank; ?>" <?php echo (isset($_POST['bank']) && $_POST['bank'] == $bank) ? 'selected' : ''; ?>>
                                    <?php echo $bank; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa fa-chevron-down select-arrow"></i>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label><i class="fa fa-map-marker-alt"></i> Select State</label>
                        <div class="input-wrapper">
                            <i class="fa fa-map"></i>
                            <select name="state" id="stateSelect" required onchange="loadDistricts()" disabled>
                                <option value="">-- First select bank --</option>
                            </select>
                            <i class="fa fa-chevron-down select-arrow"></i>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label><i class="fa fa-city"></i> Select District</label>
                        <div class="input-wrapper">
                            <i class="fa fa-location-dot"></i>
                            <select name="district" id="districtSelect" required disabled>
                                <option value="">-- First select state --</option>
                            </select>
                            <i class="fa fa-chevron-down select-arrow"></i>
                        </div>
                    </div>
                </div>
                
                <div class="button-group">
                    <button type="submit" name="search" class="btn btn-search">
                        <i class="fa fa-search"></i> Search IFSC Code
                    </button>
                    <button type="button" class="btn btn-reset" onclick="resetForm()">
                        <i class="fa fa-refresh"></i> Reset Filters
                    </button>
                </div>
            </form>
            
            <!-- Selected Criteria Display -->
            <?php if(isset($_POST['bank']) || isset($_POST['state']) || isset($_POST['district'])): ?>
            <div class="selection-badges">
                <?php if(!empty($_POST['bank'])): ?>
                <span class="selection-badge">
                    <i class="fa fa-university"></i> <?php echo $_POST['bank']; ?>
                </span>
                <?php endif; ?>
                <?php if(!empty($_POST['state'])): ?>
                <span class="selection-badge">
                    <i class="fa fa-map-marker-alt"></i> <?php echo $_POST['state']; ?>
                </span>
                <?php endif; ?>
                <?php if(!empty($_POST['district'])): ?>
                <span class="selection-badge">
                    <i class="fa fa-city"></i> <?php echo $_POST['district']; ?>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Results Card -->
        <div class="results-card">
            <div class="results-header">
                <h3>
                    <i class="fa fa-list"></i>
                    Search Results
                </h3>
                <?php if(isset($_POST['search']) && count($search_results) > 0): ?>
                <span class="result-count">
                    <i class="fa fa-code"></i> <?php echo count($search_results); ?> IFSC Codes Found
                </span>
                <?php endif; ?>
            </div>
            
            <?php echo $search_message; ?>
            
            <?php if(isset($_POST['search']) && count($search_results) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Bank Name</th>
                            <th>State</th>
                            <th>District</th>
                            <th>City</th>
                            <th>Branch</th>
                            <th>IFSC Code</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($search_results as $row): ?>
                        <tr>
                            <td>
                                <span class="bank-badge"><?php echo $row['bank_name']; ?></span>
                            </td>
                            <td><?php echo $row['state']; ?></td>
                            <td><?php echo $row['district']; ?></td>
                            <td><?php echo $row['city']; ?></td>
                            <td><?php echo $row['branch']; ?></td>
                            <td>
                                <span class="ifsc-code"><?php echo $row['ifsc_code']; ?></span>
                            </td>
                            <td>
                                <button class="copy-btn" onclick="copyIFSC('<?php echo $row['ifsc_code']; ?>')">
                                    <i class="fa fa-copy"></i> Copy
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php elseif(isset($_POST['search'])): ?>
            <div class="empty-state">
                <i class="fa fa-search"></i>
                <h3>No Branches Found</h3>
                <p>No branches match your selected criteria. Please try different filters.</p>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fa fa-hand-pointer"></i>
                <h3>Select Filters to Search</h3>
                <p>Please select Bank, State and District to find IFSC codes</p>
                <small style="color:#999;">6 Banks × 6 States × 6 Districts = 216 Branches</small>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Live Date -->
<div class="live-date" id="live-date">
    <i class="fas fa-calendar-alt"></i> Loading...
</div>

<!-- Footer Note -->
<div class="footer-note">
    <i class="fas fa-shield-alt"></i> Bank Access Only • Protected Area
</div>

<script>
// Bank to State mapping
const stateData = {
    <?php
    $bank_state_query = mysqli_query($conn, "SELECT DISTINCT bank_name, state FROM branches ORDER BY bank_name, state");
    $bank_states = [];
    while($row = mysqli_fetch_assoc($bank_state_query)) {
        $bank_states[$row['bank_name']][] = $row['state'];
    }
    
    foreach($bank_states as $bank => $states):
        echo "'" . addslashes($bank) . "': " . json_encode(array_unique($states)) . ",\n";
    endforeach;
    ?>
};

// State to District mapping
const districtData = {
    <?php
    $state_district_query = mysqli_query($conn, "SELECT DISTINCT state, district FROM branches ORDER BY state, district");
    $state_districts = [];
    while($row = mysqli_fetch_assoc($state_district_query)) {
        $state_districts[$row['state']][] = $row['district'];
    }
    
    foreach($state_districts as $state => $districts):
        echo "'" . addslashes($state) . "': " . json_encode(array_unique($districts)) . ",\n";
    endforeach;
    ?>
};

function loadStates() {
    const bankSelect = document.getElementById('bankSelect');
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');
    const selectedBank = bankSelect.value;
    
    // Reset state and district selects
    stateSelect.innerHTML = '<option value="">-- Select State --</option>';
    districtSelect.innerHTML = '<option value="">-- First select state --</option>';
    districtSelect.disabled = true;
    
    if (selectedBank && stateData[selectedBank]) {
        // Enable state select
        stateSelect.disabled = false;
        
        // Add state options
        stateData[selectedBank].forEach(state => {
            const option = document.createElement('option');
            option.value = state;
            option.textContent = state;
            stateSelect.appendChild(option);
        });
    } else {
        stateSelect.disabled = true;
    }
}

function loadDistricts() {
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');
    const selectedState = stateSelect.value;
    
    // Reset district select
    districtSelect.innerHTML = '<option value="">-- Select District --</option>';
    
    if (selectedState && districtData[selectedState]) {
        // Enable district select
        districtSelect.disabled = false;
        
        // Add district options
        districtData[selectedState].forEach(district => {
            const option = document.createElement('option');
            option.value = district;
            option.textContent = district;
            districtSelect.appendChild(option);
        });
    } else {
        districtSelect.disabled = true;
    }
}

function resetForm() {
    window.location.href = 'searchifsc.php';
}

function copyIFSC(ifsc) {
    navigator.clipboard.writeText(ifsc).then(function() {
        alert('✅ IFSC Code copied: ' + ifsc);
    }).catch(function() {
        alert('❌ Failed to copy IFSC code');
    });
}

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

// Load states and districts if form was submitted (for page reload)
window.onload = function() {
    const bankSelect = document.getElementById('bankSelect');
    const stateSelect = document.getElementById('stateSelect');
    const districtSelect = document.getElementById('districtSelect');
    
    <?php if(isset($_POST['bank'])): ?>
    // Set bank and load states
    setTimeout(function() {
        loadStates();
        
        <?php if(isset($_POST['state'])): ?>
        // Set state and load districts
        setTimeout(function() {
            stateSelect.value = '<?php echo addslashes($_POST['state']); ?>';
            loadDistricts();
            
            <?php if(isset($_POST['district'])): ?>
            // Set district
            setTimeout(function() {
                districtSelect.value = '<?php echo addslashes($_POST['district']); ?>';
            }, 100);
            <?php endif; ?>
        }, 100);
        <?php endif; ?>
    }, 100);
    <?php endif; ?>
};
</script>

</body>
</html>
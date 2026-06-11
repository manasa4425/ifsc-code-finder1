<?php
session_start();
include("db.php");

// Check if bank is logged in
if (!isset($_SESSION['bname'])) {
    header("Location: banklogin.php");
    exit();
}

$bank_name = $_SESSION['bname'] ?? 'Bank Name';
$bank_ifsc = $_SESSION['ifsc'] ?? 'IFSC Code';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Transaction Details | Bank IFSC Finder</title>
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
    max-width: 1600px;
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
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
    box-shadow: 0 10px 25px rgba(139, 92, 246, 0.4);
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
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
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
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    margin-bottom: 35px;
}

.stat-card {
    background: white;
    border-radius: 24px;
    padding: 25px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
}

.stat-card.deposit::before {
    background: linear-gradient(135deg, #10b981, #059669);
}

.stat-card.withdraw::before {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.stat-card.transfer::before {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}

.stat-card.deposit .stat-icon {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #10b981;
}

.stat-card.withdraw .stat-icon {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #ef4444;
}

.stat-card.transfer .stat-icon {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #3b82f6;
}

.stat-content h4 {
    color: #64748B;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 5px;
}

.stat-number {
    color: #1E293B;
    font-size: 28px;
    font-weight: 700;
}

.stat-label {
    color: #64748B;
    font-size: 12px;
    margin-top: 3px;
}

/* Three Column Grid */
.history-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
}

.history-card {
    background: white;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    height: fit-content;
    max-height: 600px;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
}

.history-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 35px rgba(0, 0, 0, 0.15);
}

.history-header {
    padding: 20px 25px;
    display: flex;
    align-items: center;
    gap: 15px;
    color: white;
}

.history-header.deposit {
    background: linear-gradient(135deg, #10b981, #059669);
}

.history-header.withdraw {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.history-header.transfer {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.header-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.header-text h3 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 3px;
}

.header-text p {
    font-size: 13px;
    opacity: 0.9;
}

.history-content {
    padding: 20px;
    overflow-y: auto;
    flex: 1;
    max-height: 450px;
}

/* Custom Scrollbar */
.history-content::-webkit-scrollbar {
    width: 5px;
}

.history-content::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.history-content::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 10px;
}

/* Transaction Items */
.transaction-item {
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s ease;
    border-radius: 12px;
}

.transaction-item:hover {
    background: #f8fafc;
    transform: translateX(5px);
}

.transaction-date {
    font-size: 12px;
    color: #64748B;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.transaction-date i {
    color: #667eea;
}

.transaction-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
}

.account-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.account-number {
    font-weight: 600;
    color: #1E293B;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.account-number i {
    color: #667eea;
    width: 16px;
}

.amount {
    font-weight: 700;
    font-size: 18px;
    white-space: nowrap;
}

.amount.deposit {
    color: #10b981;
}

.amount.withdraw {
    color: #ef4444;
}

.amount.transfer {
    color: #3b82f6;
}

.transfer-info {
    font-size: 12px;
    color: #64748B;
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px dashed #e2e8f0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.transfer-info i {
    color: #667eea;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #64748B;
}

.empty-state i {
    font-size: 48px;
    color: #cbd5e1;
    margin-bottom: 15px;
}

.empty-state p {
    font-size: 14px;
    font-weight: 500;
}

/* View All Button */
.view-all-btn {
    display: block;
    text-align: center;
    padding: 15px;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    color: #1E293B;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.view-all-btn:hover {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-top-color: transparent;
}

.view-all-btn i {
    transition: transform 0.3s ease;
}

.view-all-btn:hover i {
    transform: translateX(5px);
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
@media (max-width: 1024px) {
    .history-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

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
        grid-template-columns: 1fr;
    }
    
    .history-grid {
        grid-template-columns: 1fr;
    }
    
    .content-card {
        padding: 25px;
    }
    
    .bank-info-bar {
        flex-direction: column;
        align-items: flex-start;
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

/* Custom Scrollbar for whole page */
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
        <a href="searchifsc.php" class="tab-link">
            <i class="fa fa-magnifying-glass"></i> Search IFSC
        </a>
        <a href="alldeatils.php" class="tab-link active">
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
                <i class="fa fa-history"></i>
                All Transaction Details
            </h2>
            <p>Complete history of deposits, withdrawals and transfers</p>
        </div>

        <?php
        // Get totals
        $total_deposit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(deposite) as total FROM transactions"))['total'] ?? 0;
        $total_withdraw = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(withdraw) as total FROM transactions"))['total'] ?? 0;
        $total_transfer_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transactions WHERE transfer_from != ''"));
        $total_transfer_amount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM transactions WHERE transfer_from != ''"))['total'] ?? 0;
        ?>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card deposit">
                <div class="stat-icon">
                    <i class="fa fa-arrow-down"></i>
                </div>
                <div class="stat-content">
                    <h4>Total Deposits</h4>
                    <div class="stat-number">₹<?php echo number_format($total_deposit); ?></div>
                    <div class="stat-label">All time deposits</div>
                </div>
            </div>
            
            <div class="stat-card withdraw">
                <div class="stat-icon">
                    <i class="fa fa-arrow-up"></i>
                </div>
                <div class="stat-content">
                    <h4>Total Withdrawals</h4>
                    <div class="stat-number">₹<?php echo number_format($total_withdraw); ?></div>
                    <div class="stat-label">All time withdrawals</div>
                </div>
            </div>
            
            <div class="stat-card transfer">
                <div class="stat-icon">
                    <i class="fa fa-exchange-alt"></i>
                </div>
                <div class="stat-content">
                    <h4>Total Transfers</h4>
                    <div class="stat-number"><?php echo $total_transfer_count; ?></div>
                    <div class="stat-label">Transactions • ₹<?php echo number_format($total_transfer_amount); ?></div>
                </div>
            </div>
        </div>

        <!-- Three Column History -->
        <div class="history-grid">
            <?php
            // Get deposits
            $deposits = mysqli_query($conn, "SELECT * FROM transactions WHERE deposite > 0 ORDER BY id DESC LIMIT 10");
            ?>

            <!-- Deposit History Column -->
            <div class="history-card">
                <div class="history-header deposit">
                    <div class="header-icon">
                        <i class="fa fa-arrow-down"></i>
                    </div>
                    <div class="header-text">
                        <h3>Deposit History</h3>
                        <p>Recent deposits</p>
                    </div>
                </div>
                
                <div class="history-content">
                    <?php if(mysqli_num_rows($deposits) > 0): ?>
                        <?php while($dep = mysqli_fetch_assoc($deposits)): ?>
                        <div class="transaction-item">
                            <div class="transaction-date">
                                <i class="fa fa-calendar"></i>
                                <?php echo date('d M Y H:i', strtotime($dep['date'] ?? date('Y-m-d H:i:s'))); ?>
                            </div>
                            <div class="transaction-details">
                                <span class="account-number">
                                    <i class="fa fa-university"></i>
                                    <?php echo $dep['accountno'] ?? 'N/A'; ?>
                                </span>
                                <span class="amount deposit">
                                    +₹<?php echo number_format($dep['deposite'] ?? 0); ?>
                                </span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa fa-arrow-down"></i>
                            <p>No deposits yet</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <a href="deposite.php" class="view-all-btn">
                    <span>New Deposit</span>
                    <i class="fa fa-arrow-right"></i>
                </a>
            </div>

            <?php
            // Get withdraws
            $withdraws = mysqli_query($conn, "SELECT * FROM transactions WHERE withdraw > 0 ORDER BY id DESC LIMIT 10");
            ?>

            <!-- Withdraw History Column -->
            <div class="history-card">
                <div class="history-header withdraw">
                    <div class="header-icon">
                        <i class="fa fa-arrow-up"></i>
                    </div>
                    <div class="header-text">
                        <h3>Withdraw History</h3>
                        <p>Recent withdrawals</p>
                    </div>
                </div>
                
                <div class="history-content">
                    <?php if(mysqli_num_rows($withdraws) > 0): ?>
                        <?php while($wd = mysqli_fetch_assoc($withdraws)): ?>
                        <div class="transaction-item">
                            <div class="transaction-date">
                                <i class="fa fa-calendar"></i>
                                <?php echo date('d M Y H:i', strtotime($wd['date'] ?? date('Y-m-d H:i:s'))); ?>
                            </div>
                            <div class="transaction-details">
                                <span class="account-number">
                                    <i class="fa fa-university"></i>
                                    <?php echo $wd['accountno'] ?? 'N/A'; ?>
                                </span>
                                <span class="amount withdraw">
                                    -₹<?php echo number_format($wd['withdraw'] ?? 0); ?>
                                </span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa fa-arrow-up"></i>
                            <p>No withdrawals yet</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <a href="withdraw.php" class="view-all-btn">
                    <span>New Withdraw</span>
                    <i class="fa fa-arrow-right"></i>
                </a>
            </div>

            <?php
            // Get transfers
            $transfers = mysqli_query($conn, "SELECT * FROM transactions WHERE transfer_from != '' AND transfer_to != '' ORDER BY id DESC LIMIT 10");
            ?>

            <!-- Transfer History Column -->
            <div class="history-card">
                <div class="history-header transfer">
                    <div class="header-icon">
                        <i class="fa fa-exchange-alt"></i>
                    </div>
                    <div class="header-text">
                        <h3>Transfer History</h3>
                        <p>Recent transfers</p>
                    </div>
                </div>
                
                <div class="history-content">
                    <?php if(mysqli_num_rows($transfers) > 0): ?>
                        <?php while($tr = mysqli_fetch_assoc($transfers)): ?>
                        <div class="transaction-item">
                            <div class="transaction-date">
                                <i class="fa fa-calendar"></i>
                                <?php echo date('d M Y H:i', strtotime($tr['date'] ?? date('Y-m-d H:i:s'))); ?>
                            </div>
                            <div class="transaction-details">
                                <div class="account-info">
                                    <span class="account-number">
                                        <i class="fa fa-arrow-right-from-bracket"></i>
                                        <?php echo $tr['transfer_from'] ?? 'N/A'; ?>
                                    </span>
                                    <span class="account-number">
                                        <i class="fa fa-arrow-right-to-bracket"></i>
                                        <?php echo $tr['transfer_to'] ?? 'N/A'; ?>
                                    </span>
                                </div>
                                <span class="amount transfer">
                                    ₹<?php echo number_format($tr['amount'] ?? 0); ?>
                                </span>
                            </div>
                            <div class="transfer-info">
                                <i class="fa fa-qrcode"></i> IFSC Transfer
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa fa-exchange-alt"></i>
                            <p>No transfers yet</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <a href="transaction.php" class="view-all-btn">
                    <span>New Transfer</span>
                    <i class="fa fa-arrow-right"></i>
                </a>
            </div>
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
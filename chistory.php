<?php
session_start();
include("db.php");

// Check if CUSTOMER is logged in
if (!isset($_SESSION['accno'])) {
    header("Location: customerlogin.php");
    exit();
}

$accno = $_SESSION['accno'];

// Get customer details
$customer_query = mysqli_query($conn, "SELECT * FROM account WHERE accno='$accno'");
$customer = mysqli_fetch_assoc($customer_query);
$bank_name = $customer['bankname'] ?? 'Bank Name';
$bank_ifsc = $customer['ifsc'] ?? 'IFSC Code';

// Get all transactions for this customer
$transactions = mysqli_query($conn, 
    "SELECT * FROM transactions 
     WHERE accountno='$accno' 
     ORDER BY id DESC"
);

// Calculate totals
$total_deposits = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(deposite) as total FROM transactions WHERE accountno='$accno'"))['total'] ?? 0;
$total_withdrawals = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(withdraw) as total FROM transactions WHERE accountno='$accno'"))['total'] ?? 0;
$transaction_count = mysqli_num_rows($transactions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Transaction History | Customer - Bank IFSC Finder</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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

/* Welcome Banner */
.welcome-banner {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 50px;
    padding: 12px 30px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
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

/* Main Container */
.main-container {
    max-width: 1200px;
    margin: 0 auto;
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
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 5px;
}

.stat-label {
    color: #64748b;
    font-size: 13px;
}

.stat-card.deposit .stat-value {
    color: #10b981;
}

.stat-card.withdraw .stat-value {
    color: #dc2626;
}

/* History Card */
.history-card {
    background: white;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}

.history-header {
    padding: 20px 24px;
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    color: white;
    display: flex;
    align-items: center;
    gap: 15px;
}

.history-header i { font-size: 28px; }
.history-header h2 { font-size: 20px; font-weight: 700; }

.history-content { padding: 24px; }

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    text-align: left;
    padding: 12px;
    background: #f8fafc;
    color: #1e293b;
    font-size: 14px;
    font-weight: 600;
}

td {
    padding: 12px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    font-size: 14px;
}

tr:hover { background: #f8fafc; }

.credit { color: #059669; font-weight: 600; }
.debit { color: #dc2626; font-weight: 600; }

.empty-state {
    text-align: center;
    padding: 50px;
    color: #94a3b8;
}

.empty-state i { font-size: 48px; margin-bottom: 15px; }

/* Current Balance */
.current-balance {
    background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
    border-radius: 16px;
    padding: 15px 25px;
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 4px solid #2563EB;
}

.current-balance span:first-child {
    color: #1e293b;
    font-weight: 600;
    font-size: 16px;
}

.current-balance .balance-amount {
    color: #2563eb;
    font-weight: 700;
    font-size: 24px;
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
    
    .header {
        flex-direction: column;
        gap: 15px;
    }
    
    .header-nav {
        width: 100%;
        justify-content: center;
    }
    
    .welcome-banner {
        flex-direction: column;
        text-align: center;
    }
    
    .content-card {
        padding: 25px;
    }
    
    .bank-info-bar {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .current-balance {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    table { font-size: 12px; }
    th, td { padding: 8px; }
    
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
        <a href="customer_profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="clogout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <i class="fas fa-user-circle"></i>
    <span>Welcome,</span>
    <span class="bank-name"><?php echo htmlspecialchars($customer['name'] ?? 'Customer'); ?></span>
    
    <div class="status-container">
        <span class="status-dot"></span>
        <span class="status-text">ACCOUNT ACCESS</span>
    </div>
</div>

<!-- Main Container -->
<div class="main-container">
    
    <!-- Content Card -->
    <div class="content-card">
        
        <!-- Bank Info Bar -->
        <div class="bank-info-bar">
            <div class="bank-info-item">
                <div class="bank-info-icon">
                    <i class="fa fa-user"></i>
                </div>
                <div class="bank-info-text">
                    <h4>ACCOUNT HOLDER</h4>
                    <span><?php echo htmlspecialchars($customer['name'] ?? 'N/A'); ?></span>
                </div>
            </div>
            
            <div class="bank-info-item">
                <div class="bank-info-icon">
                    <i class="fa fa-credit-card"></i>
                </div>
                <div class="bank-info-text">
                    <h4>ACCOUNT NUMBER</h4>
                    <span><?php echo $customer['accno'] ?? 'N/A'; ?></span>
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
                Transaction History
            </h2>
            <p>Complete history of your deposits and withdrawals</p>
        </div>

        <!-- Current Balance -->
        <div class="current-balance">
            <span><i class="fa fa-wallet"></i> Your Current Balance</span>
            <span class="balance-amount">₹<?php echo number_format($customer['balance'] ?? 0, 2); ?></span>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $transaction_count; ?></div>
                <div class="stat-label">Total Transactions</div>
            </div>
            <div class="stat-card deposit">
                <div class="stat-value">₹<?php echo number_format($total_deposits); ?></div>
                <div class="stat-label">Total Deposits</div>
            </div>
            <div class="stat-card withdraw">
                <div class="stat-value">₹<?php echo number_format($total_withdrawals); ?></div>
                <div class="stat-label">Total Withdrawals</div>
            </div>
        </div>

        <!-- History Table -->
        <div class="history-card">
            <div class="history-header">
                <i class="fas fa-history"></i>
                <h2>Transaction History</h2>
            </div>
            <div class="history-content">
                <?php if(mysqli_num_rows($transactions) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Transaction Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($transactions)): 
                                if($row['deposite'] > 0) {
                                    $type = 'Deposit';
                                    $amount = '+ ₹' . number_format($row['deposite']);
                                    $class = 'credit';
                                } elseif($row['withdraw'] > 0) {
                                    $type = 'Withdrawal';
                                    $amount = '- ₹' . number_format($row['withdraw']);
                                    $class = 'debit';
                                }
                            ?>
                            <tr>
                                <td><?php echo date('d M Y, h:i A', strtotime($row['date'])); ?></td>
                                <td><?php echo $type; ?></td>
                                <td class="<?php echo $class; ?>"><?php echo $amount; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-history"></i>
                        <h3>No Transactions Yet</h3>
                        <p>Your transaction history will appear here once you make deposits or withdrawals.</p>
                    </div>
                <?php endif; ?>
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
    <i class="fas fa-shield-alt"></i> Customer Access • Transaction History
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
</script>

</body>
</html>
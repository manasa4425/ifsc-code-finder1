<?php
session_start();
include("db.php");

// Check if CUSTOMER is logged in (not bank)
if (!isset($_SESSION['accno'])) {
    header("Location: customerlogin.php");
    exit();
}

$accno = $_SESSION['accno'];
$bank_name = $_SESSION['bname'] ?? 'Bank Name'; // Optional, if you store bank name in customer session
$bank_ifsc = $_SESSION['ifsc'] ?? 'IFSC Code';

// Get customer details for display
$customer_query = mysqli_query($conn, "SELECT * FROM account WHERE accno='$accno'");
$customer = mysqli_fetch_assoc($customer_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Deposit Amount | Customer - Bank IFSC Finder</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
/* Copy all your existing styles here - they're fine */
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
    margin-bottom: 25px;
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 36px;
}

.page-header p {
    color: #64748B;
    font-size: 15px;
}

/* Deposit Card */
.deposit-card {
    background: white;
    border-radius: 24px;
    padding: 35px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

/* Balance Preview */
.balance-preview {
    background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 15px;
    border-left: 4px solid #2563EB;
}

.balance-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.balance-content h4 {
    color: #1E293B;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 3px;
}

.balance-content span {
    color: #64748B;
    font-size: 13px;
}

/* Form */
.input-group {
    margin-bottom: 25px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #1E293B;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

label i {
    color: #667eea;
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
    color: #667eea;
    font-size: 18px;
    z-index: 1;
}

input {
    width: 100%;
    padding: 16px 16px 16px 50px;
    border: 2px solid #E2E8F0;
    border-radius: 16px;
    outline: none;
    background: white;
    color: #1E293B;
    font-size: 16px;
    transition: all 0.3s;
}

input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

input::placeholder {
    color: #94A3B8;
}

/* Amount Display */
.amount-display {
    background: #F8FAFC;
    padding: 15px 20px;
    border-radius: 12px;
    margin: 20px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid #E2E8F0;
}

.amount-display span:first-child {
    color: #64748B;
    font-weight: 500;
}

.amount-display .current-balance {
    color: #10B981;
    font-weight: 700;
    font-size: 18px;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    padding: 18px;
    border: none;
    border-radius: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 700;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 15px;
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.submit-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.submit-btn:hover::before {
    left: 100%;
}

.submit-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 30px rgba(102, 126, 234, 0.4);
}

/* Security Note */
.security-note {
    margin-top: 25px;
    padding: 15px;
    background: #F8FAFC;
    border-radius: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    color: #64748B;
    font-size: 13px;
    border: 1px solid #E2E8F0;
}

.security-note i {
    color: #667eea;
    font-size: 16px;
}

.security-note i:last-child {
    color: #10B981;
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

/* Quick Info */
.quick-info {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.info-chip {
    background: #F1F5F9;
    padding: 8px 15px;
    border-radius: 30px;
    color: #1E293B;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.info-chip i {
    color: #667eea;
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
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
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
    
    .deposit-card {
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

@media (max-width: 480px) {
    .page-header h2 {
        font-size: 26px;
    }
    
    .security-note {
        flex-direction: column;
        gap: 8px;
        text-align: center;
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
                <i class="fa-solid fa-money-bill-transfer"></i>
                Deposit Amount
            </h2>
            <p>Securely credit money into your account</p>
        </div>
        
        <!-- Quick Info Chips -->
        <div class="quick-info">
            <div class="info-chip">
                <i class="fa fa-clock"></i> 24/7 Transactions
            </div>
            <div class="info-chip">
                <i class="fa fa-shield-alt"></i> Secure & Encrypted
            </div>
            <div class="info-chip">
                <i class="fa fa-bolt"></i> Instant Processing
            </div>
        </div>
        
        <!-- Deposit Card -->
        <div class="deposit-card">
            
            <!-- Balance Preview -->
            <div class="balance-preview">
                <div class="balance-icon">
                    <i class="fa fa-wallet"></i>
                </div>
                <div class="balance-content">
                    <h4>Your Current Balance</h4>
                    <span>₹<?php echo number_format($customer['balance'] ?? 0, 2); ?></span>
                </div>
            </div>
            
            <!-- Deposit Form -->
            <form method="post">
                <input type="hidden" name="accno" value="<?php echo $customer['accno'] ?? ''; ?>">
                
                <div class="input-group">
                    <label><i class="fa fa-indian-rupee-sign"></i> Deposit Amount</label>
                    <div class="input-wrapper">
                        <i class="fa fa-money-bill-wave"></i>
                        <input type="number" name="amount" placeholder="Enter amount to deposit" min="1" max="100000" step="0.01" required>
                    </div>
                    <small style="color: #64748b; display: block; margin-top: 5px;">Maximum deposit: ₹1,00,000 per transaction</small>
                </div>
                
                <button type="submit" name="submit" class="submit-btn">
                    <i class="fa fa-check-circle"></i>
                    Deposit Now
                    <i class="fa fa-arrow-right"></i>
                </button>
                
                <div class="security-note">
                    <i class="fa fa-lock"></i>
                    <span>Transaction is secure & recorded in ledger</span>
                    <i class="fa fa-shield-alt"></i>
                </div>
            </form>
            
        </div>
    </div>
</div>

<!-- Live Date -->
<div class="live-date" id="live-date">
    <i class="fas fa-calendar-alt"></i> Loading...
</div>

<!-- Footer Note -->
<div class="footer-note">
    <i class="fas fa-shield-alt"></i> Customer Access • Protected Area
</div>

<?php
// DEPOSIT PROCESSING
if (isset($_POST['submit'])) {
    $accno = mysqli_real_escape_string($conn, $_POST['accno']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);

    // Validate amount
    if ($amount <= 0) {
        echo "<script>alert('❌ Please enter a valid amount greater than 0');</script>";
    } elseif ($amount > 100000) {
        echo "<script>alert('❌ Maximum deposit amount is ₹1,00,000');</script>";
    } else {
        // Check if account exists
        $q = "SELECT balance FROM account WHERE accno='$accno'";
        $res = mysqli_query($conn, $q);

        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            $old_balance = $row['balance'];
            $newbal = $old_balance + $amount;

            // Start transaction
            mysqli_begin_transaction($conn);
            
            try {
                // Update balance
                $update = mysqli_query($conn, "UPDATE account SET balance='$newbal' WHERE accno='$accno'");
                
                // Record transaction
                $q2 = "INSERT INTO transactions (accountno, deposite, date) VALUES('$accno', '$amount', NOW())";
                mysqli_query($conn, $q2);

                if ($update) {
                    mysqli_commit($conn);
                    echo "<script>alert('✅ Deposit Successful! ₹" . number_format($amount, 2) . " credited to your account');window.location='cdeposit.php';</script>";
                } else {
                    mysqli_rollback($conn);
                    echo "<script>alert('❌ Transaction failed. Please try again.');</script>";
                }
            } catch (Exception $e) {
                mysqli_rollback($conn);
                echo "<script>alert('❌ Error: " . $e->getMessage() . "');</script>";
            }
        } else {
            echo "<script>alert('❌ Account not found. Please contact support.');</script>";
        }
    }
}
?>

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
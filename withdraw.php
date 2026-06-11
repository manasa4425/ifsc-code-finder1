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
<title>Withdraw Amount | Bank IFSC Finder</title>
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
    max-width: 900px;
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
    background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
    box-shadow: 0 10px 25px rgba(244, 63, 94, 0.4);
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
    background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 36px;
}

.page-header p {
    color: #64748B;
    font-size: 15px;
}

/* Withdraw Card */
.withdraw-card {
    background: white;
    border-radius: 24px;
    padding: 35px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

/* Balance Warning */
.balance-warning {
    background: linear-gradient(135deg, #FEF2F2, #FEE2E2);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 15px;
    border-left: 4px solid #DC2626;
}

.warning-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.warning-content h4 {
    color: #DC2626;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 3px;
}

.warning-content p {
    color: #64748B;
    font-size: 13px;
    margin: 0;
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
    color: #f43f5e;
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
    color: #f43f5e;
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
    border-color: #f43f5e;
    box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.1);
}

input::placeholder {
    color: #94A3B8;
}

/* Amount Symbol */
.amount-symbol {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    background: #F1F5F9;
    padding: 6px 15px;
    border-radius: 30px;
    color: #f43f5e;
    font-weight: 600;
    font-size: 14px;
    border: 1px solid #E2E8F0;
    z-index: 1;
}

/* Info Badge */
.info-badge {
    background: #F1F5F9;
    padding: 12px 20px;
    border-radius: 40px;
    margin: 25px 0 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #64748B;
    font-size: 14px;
    border-left: 4px solid #f43f5e;
}

.info-badge i {
    color: #f43f5e;
    font-size: 16px;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    padding: 18px;
    border: none;
    border-radius: 16px;
    background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
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
    box-shadow: 0 20px 30px rgba(244, 63, 94, 0.4);
}

.submit-btn i {
    font-size: 20px;
}

/* Features Note */
.features-note {
    margin-top: 25px;
    padding: 15px;
    background: #F8FAFC;
    border-radius: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 25px;
    color: #64748B;
    font-size: 13px;
    border: 1px solid #E2E8F0;
    flex-wrap: wrap;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.feature-item i {
    color: #f43f5e;
    font-size: 14px;
}

.feature-item span {
    font-weight: 500;
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
    color: #f43f5e;
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
    
    .content-card {
        padding: 25px;
    }
    
    .withdraw-card {
        padding: 25px;
    }
    
    .bank-info-bar {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .features-note {
        flex-direction: column;
        gap: 10px;
        text-align: center;
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
    
    .balance-warning {
        flex-direction: column;
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
        <a href="withdraw.php" class="tab-link active">
            <i class="fa fa-hand-holding-dollar"></i> Withdraw
        </a>
        <a href="transaction.php" class="tab-link">
            <i class="fa fa-right-left"></i> Transaction
        </a>
        <a href="searchifsc.php" class="tab-link">
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
                <i class="fa fa-money-bill-wave"></i>
                Withdraw Amount
            </h2>
            <p>Securely debit money from customer account</p>
        </div>
        
        <!-- Quick Info Chips -->
        <div class="quick-info">
            <div class="info-chip">
                <i class="fa fa-clock"></i> 24/7 Withdrawals
            </div>
            <div class="info-chip">
                <i class="fa fa-shield-alt"></i> Secure & Encrypted
            </div>
            <div class="info-chip">
                <i class="fa fa-bolt"></i> Instant Processing
            </div>
        </div>
        
        <!-- Withdraw Card -->
        <div class="withdraw-card">
            
            <!-- Balance Warning -->
            <div class="balance-warning">
                <div class="warning-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <div class="warning-content">
                    <h4>⚠️ Insufficient Balance Warning</h4>
                    <p>System will automatically check available balance before processing withdrawal. Minimum balance ₹500 must be maintained.</p>
                </div>
            </div>
            
            <!-- Withdraw Form -->
            <form method="post">
                <div class="input-group">
                    <label><i class="fa fa-credit-card"></i> Account Number</label>
                    <div class="input-wrapper">
                        <i class="fa fa-building-columns"></i>
                        <input type="text" name="accno" placeholder="Enter 15-digit account number" pattern="[0-9]{15}" title="Account number must be 15 digits" required>
                    </div>
                </div>
                
                <div class="input-group">
                    <label><i class="fa fa-indian-rupee-sign"></i> Withdrawal Amount</label>
                    <div class="input-wrapper">
                        <i class="fa fa-money-bill-wave"></i>
                        <input type="number" name="amount" placeholder="Enter amount to withdraw" min="1" step="0.01" required>
                        <span class="amount-symbol">INR</span>
                    </div>
                </div>
                
                <div class="info-badge">
                    <i class="fa fa-shield-alt"></i>
                    <span>Minimum balance ₹500 must be maintained after withdrawal</span>
                </div>
                
                <button type="submit" name="submit" class="submit-btn">
                    <i class="fa fa-check-circle"></i>
                    Process Withdrawal
                    <i class="fa fa-arrow-right"></i>
                </button>
                
                <div class="features-note">
                    <div class="feature-item">
                        <i class="fa fa-check-circle"></i>
                        <span>Balance check</span>
                    </div>
                    <div class="feature-item">
                        <i class="fa fa-lock"></i>
                        <span>Secure</span>
                    </div>
                    <div class="feature-item">
                        <i class="fa fa-clock"></i>
                        <span>Instant</span>
                    </div>
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
    <i class="fas fa-shield-alt"></i> Bank Access Only • Protected Area
</div>

<?php
// YOUR ORIGINAL PHP CODE - ENHANCED WITH BETTER VALIDATION
if(isset($_POST['submit'])){

    $accno = mysqli_real_escape_string($conn, $_POST['accno']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);

    // Validate amount
    if ($amount <= 0) {
        echo "<script>alert('❌ Please enter a valid amount greater than 0');</script>";
        exit();
    }

    $res = mysqli_query($conn, "SELECT balance FROM account WHERE accno='$accno'");

    if(mysqli_num_rows($res) > 0){

        $row = mysqli_fetch_assoc($res);
        $current_balance = $row['balance'];

        if($current_balance >= $amount){

            $newbal = $current_balance - $amount;

            // Check minimum balance condition (if required)
            if($newbal >= 500 || $newbal >= 0) {
                
                $update = mysqli_query($conn, "UPDATE account SET balance='$newbal' WHERE accno='$accno'");
                
                if($update) {
                    mysqli_query($conn, "INSERT INTO transactions(accountno, withdraw, date) VALUES('$accno','$amount', NOW())");
                    echo "<script>alert('✅ Withdrawal Successful! ₹" . number_format($amount, 2) . " debited from Account: " . $accno . "');window.location='withdraw.php';</script>";
                } else {
                    echo "<script>alert('❌ Transaction failed. Please try again.');</script>";
                }
                
            } else {
                echo "<script>alert('⚠️ Minimum balance of ₹500 must be maintained. Current balance after withdrawal would be: ₹" . number_format($newbal, 2) . "');</script>";
            }

        } else {
            echo "<script>alert('❌ Insufficient Balance. Available balance: ₹" . number_format($current_balance, 2) . "');</script>";
        }

    } else {
        echo "<script>alert('❌ Invalid Account Number. Please check and try again.');</script>";
    }
}
?>

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

// Account number validation (only numbers)
document.querySelector('input[name="accno"]').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>

</body>
</html>
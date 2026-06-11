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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Account | Bank IFSC Finder</title>
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
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

/* Form Header */
.form-header {
    text-align: center;
    margin-bottom: 25px;
}

.form-header h2 {
    color: #1E293B;
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.form-header h2 i {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 32px;
}

.form-header p {
    color: #64748B;
    font-size: 14px;
}

/* Info Box */
.info-box {
    background: #EFF6FF;
    border-left: 4px solid #2563EB;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.info-box i {
    font-size: 24px;
    color: #2563EB;
}

.info-box-content h4 {
    color: #1E293B;
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 3px;
}

.info-box-content p {
    color: #64748B;
    font-size: 13px;
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}

.form-group {
    margin-bottom: 0;
}

.form-group.full-width {
    grid-column: span 2;
}

label {
    display: block;
    margin-bottom: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #1E293B;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

label i {
    color: #667eea;
    margin-right: 5px;
    font-size: 12px;
}

.input-wrapper {
    position: relative;
}

.input-wrapper i {
    position: absolute;
    top: 50%;
    left: 12px;
    transform: translateY(-50%);
    color: #667eea;
    font-size: 14px;
    z-index: 1;
}

input, textarea, select {
    width: 100%;
    padding: 12px 12px 12px 38px;
    border: 2px solid #E2E8F0;
    border-radius: 12px;
    outline: none;
    background: white;
    color: #1E293B;
    font-size: 14px;
    transition: all 0.3s;
}

textarea {
    padding: 12px;
    resize: vertical;
    min-height: 80px;
}

input:focus, textarea:focus, select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}

input[readonly] {
    background: #F8FAFC;
    color: #64748B;
    border-color: #E2E8F0;
}

/* Radio Group */
.radio-group {
    display: flex;
    gap: 25px;
    padding: 8px 0 8px 38px;
}

.radio-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    text-transform: none;
    letter-spacing: normal;
    cursor: pointer;
    margin-bottom: 0;
    font-size: 14px;
}

.radio-group input[type="radio"] {
    width: auto;
    margin: 0;
    accent-color: #667eea;
    padding: 0;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    padding: 16px;
    border: none;
    border-radius: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
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
    box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
}

.submit-btn i {
    font-size: 18px;
}

/* Alert Messages */
.alert {
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideIn 0.3s;
}

.alert.success {
    background: #F0FDF4;
    color: #10B981;
    border-left: 4px solid #10B981;
}

.alert.error {
    background: #FEF2F2;
    color: #DC2626;
    border-left: 4px solid #DC2626;
}

.alert i {
    font-size: 20px;
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
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-group.full-width {
        grid-column: span 1;
    }
    
    .content-card {
        padding: 25px;
    }
    
    .bank-info-bar {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .radio-group {
        padding-left: 0;
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
        <a href="addaccount.php" class="tab-link active">
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
        
        <!-- Form Header -->
        <div class="form-header">
            <h2>
                <i class="fa fa-user-plus"></i>
                Add New Account
            </h2>
            <p>Create a new customer account</p>
        </div>
        
        <!-- Info Box -->
        <div class="info-box">
            <i class="fa fa-info-circle"></i>
            <div class="info-box-content">
                <h4>Account Creation Guidelines</h4>
                <p>All fields are mandatory. Account number must be 15 digits, Aadhar must be 12 digits.</p>
            </div>
        </div>
        
        <!-- Form -->
        <form method="post">
            <div class="form-grid">
                <!-- Full Name -->
                <div class="form-group">
                    <label><i class="fa fa-user"></i> Full Name</label>
                    <div class="input-wrapper">
                        <i class="fa fa-user-circle"></i>
                        <input type="text" name="s1" placeholder="Enter customer name" required>
                    </div>
                </div>
                
                <!-- Place/Address -->
                <div class="form-group">
                    <label><i class="fa fa-map-marker"></i> Place/Address</label>
                    <div class="input-wrapper">
                        <i class="fa fa-location-dot"></i>
                        <input type="text" name="s2" placeholder="Enter address" required>
                    </div>
                </div>
                
                <!-- Email -->
                <div class="form-group">
                    <label><i class="fa fa-envelope"></i> Email</label>
                    <div class="input-wrapper">
                        <i class="fa fa-at"></i>
                        <input type="email" name="s3" placeholder="Enter email" required>
                    </div>
                </div>
                
                <!-- Phone -->
                <div class="form-group">
                    <label><i class="fa fa-phone"></i> Phone</label>
                    <div class="input-wrapper">
                        <i class="fa fa-phone-alt"></i>
                        <input type="text" name="s4" pattern="[0-9]{10}" placeholder="10 digit mobile" required>
                    </div>
                </div>
                
                <!-- Aadhar Number -->
                <div class="form-group">
                    <label><i class="fa fa-id-card"></i> Aadhar Number</label>
                    <div class="input-wrapper">
                        <i class="fa fa-id-card"></i>
                        <input type="text" name="s5" pattern="[0-9]{12}" placeholder="12 digit Aadhar" required>
                    </div>
                </div>
                
                <!-- Gender -->
                <div class="form-group">
                    <label><i class="fa fa-venus-mars"></i> Gender</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="s6" value="Male" checked> Male
                        </label>
                        <label>
                            <input type="radio" name="s6" value="Female"> Female
                        </label>
                    </div>
                </div>
                
                <!-- Balance Amount -->
                <div class="form-group">
                    <label><i class="fa fa-rupee-sign"></i> Balance Amount</label>
                    <div class="input-wrapper">
                        <i class="fa fa-coins"></i>
                        <input type="number" name="s7" placeholder="Initial deposit" required>
                    </div>
                </div>
                
                <!-- Password -->
                <div class="form-group">
                    <label><i class="fa fa-key"></i> Password</label>
                    <div class="input-wrapper">
                        <i class="fa fa-lock"></i>
                        <input type="text" name="s8" placeholder="Set password" required>
                    </div>
                </div>
                
                <!-- Account Number -->
                <div class="form-group">
                    <label><i class="fa fa-credit-card"></i> Account Number</label>
                    <div class="input-wrapper">
                        <i class="fa fa-barcode"></i>
                        <input type="text" name="s9" pattern="[0-9]{15}" placeholder="15 digit account no" required>
                    </div>
                </div>
                
                <!-- PIN -->
                <div class="form-group">
                    <label><i class="fa fa-lock"></i> PIN</label>
                    <div class="input-wrapper">
                        <i class="fa fa-key"></i>
                        <input type="number" name="s10" placeholder="4-6 digit PIN" required>
                    </div>
                </div>
                
                <!-- Max Amount (Optional) -->
                <div class="form-group full-width">
                    <label><i class="fa fa-chart-line"></i> Max Amount (Optional)</label>
                    <div class="input-wrapper">
                        <i class="fa fa-arrow-up"></i>
                        <input type="text" name="s11" placeholder="Maximum transaction limit">
                    </div>
                </div>
            </div>
            
            <button type="submit" name="Submit" class="submit-btn">
                <i class="fa fa-check-circle"></i>
                Create Account
                <i class="fa fa-arrow-right"></i>
            </button>
        </form>
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
if(isset($_POST["Submit"])){

    $s1 = mysqli_real_escape_string($conn, $_POST["s1"]);
    $s2 = mysqli_real_escape_string($conn, $_POST["s2"]);
    $s3 = mysqli_real_escape_string($conn, $_POST["s3"]);
    $s4 = mysqli_real_escape_string($conn, $_POST["s4"]);
    $s5 = mysqli_real_escape_string($conn, $_POST["s5"]);
    $s6 = mysqli_real_escape_string($conn, $_POST["s6"]);
    $s7 = mysqli_real_escape_string($conn, $_POST["s7"]);
    $s8 = mysqli_real_escape_string($conn, $_POST["s8"]);
    $s9 = mysqli_real_escape_string($conn, $_POST["s9"]);
    $s10 = mysqli_real_escape_string($conn, $_POST["s10"]);
    $s11 = mysqli_real_escape_string($conn, $_POST["s11"]);
    
    $s12 = $_SESSION['bname'];
    $s13 = $_SESSION['ifsc'];

    // Check if account number already exists
    $check = mysqli_query($conn, "SELECT * FROM account WHERE accno='$s9'");
    if(mysqli_num_rows($check) > 0) {
        echo "<script>alert('❌ Account number already exists!');</script>";
    } else {
        $sql = "INSERT INTO account(name,address,email,phone,adharno,gender,balance,password,accno,pin,maxamount,bankname,ifsc)
        VALUES('$s1','$s2','$s3','$s4','$s5','$s6','$s7','$s8','$s9','$s10','$s11','$s12','$s13')";

        if(mysqli_query($conn, $sql)){
            echo "<script>alert('✅ Account Created Successfully');window.location='addaccount.php';</script>";
        } else {
            echo "<script>alert('❌ Error: " . mysqli_error($conn) . "');</script>";
        }
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
</script>

</body>
</html>
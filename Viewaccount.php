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

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// First, let's check the actual structure of the account table
$table_info = mysqli_query($conn, "DESCRIBE account");
if (!$table_info) {
    // Table doesn't exist - create it with proper structure
    $create_table = "CREATE TABLE IF NOT EXISTS account (
        id INT AUTO_INCREMENT PRIMARY KEY,
        bank_name VARCHAR(100),
        name VARCHAR(100) NOT NULL,
        address TEXT NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone VARCHAR(10) NOT NULL,
        adharno VARCHAR(12) NOT NULL UNIQUE,
        gender VARCHAR(10) NOT NULL,
        balance DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        password VARCHAR(255) NOT NULL,
        accno VARCHAR(15) NOT NULL UNIQUE,
        pin VARCHAR(4) NOT NULL,
        maxamount DECIMAL(10,2) NOT NULL DEFAULT 100000.00,
        status VARCHAR(20) DEFAULT 'active',
        profile_photo VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($conn, $create_table)) {
        die("Error creating table: " . mysqli_error($conn));
    }
} else {
    // Table exists, check if bank_name column exists
    $columns = [];
    while($col = mysqli_fetch_assoc($table_info)) {
        $columns[] = $col['Field'];
    }
    
    // If bank_name column doesn't exist, add it
    if (!in_array('bank_name', $columns)) {
        // Find the position to add bank_name after the first column
        $first_column = $columns[0] ?? 'id';
        $alter_table = "ALTER TABLE account ADD COLUMN bank_name VARCHAR(100) AFTER $first_column";
        
        if (!mysqli_query($conn, $alter_table)) {
            // If that fails, try adding it without position
            $alter_table = "ALTER TABLE account ADD COLUMN bank_name VARCHAR(100)";
            if (!mysqli_query($conn, $alter_table)) {
                die("Error adding bank_name column: " . mysqli_error($conn));
            }
        }
    }
}

// Update existing accounts to set bank_name if it's NULL (optional)
$update_null = "UPDATE account SET bank_name='$bank_name' WHERE bank_name IS NULL OR bank_name=''";
mysqli_query($conn, $update_null);

// Get accounts only for this specific bank
$sql = "SELECT * FROM account WHERE bank_name = '$bank_name' ORDER BY ";
    
// Check which column to order by
if (in_array('id', $columns ?? [])) {
    $sql .= "id DESC";
} elseif (in_array('accno', $columns ?? [])) {
    $sql .= "accno DESC";
} else {
    $sql .= "created_at DESC";
}

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error fetching accounts: " . mysqli_error($conn));
}

$row_count = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Accounts | <?php echo htmlspecialchars($bank_name); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
/* ALL YOUR EXISTING STYLES REMAIN EXACTLY THE SAME */
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
    padding: 30px;
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
    font-size: 28px;
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
    font-size: 32px;
}

.page-header p {
    color: #64748B;
    font-size: 14px;
}

/* Stats Row */
.stats-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.stat-box {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 12px 20px;
    border-radius: 40px;
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid #e2e8f0;
}

.stat-box i {
    color: #667eea;
    font-size: 18px;
}

.stat-box span {
    color: #1E293B;
    font-weight: 600;
    font-size: 14px;
}

.stat-box strong {
    color: #667eea;
    font-size: 18px;
    margin-left: 5px;
}

/* Search Box */
.search-box {
    position: relative;
    width: 300px;
}

.search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #667eea;
    font-size: 16px;
}

.search-box input {
    width: 100%;
    padding: 12px 12px 12px 45px;
    border: 2px solid #E2E8F0;
    border-radius: 40px;
    outline: none;
    background: white;
    color: #1E293B;
    font-size: 14px;
    transition: all 0.3s;
}

.search-box input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}

/* Table Container */
.table-container {
    overflow-x: auto;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    background: white;
}

table {
    width: 100%;
    min-width: 1600px;
    border-collapse: collapse;
}

th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 10px;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
}

td {
    padding: 12px 8px;
    border-bottom: 1px solid #f0f0f0;
    color: #1E293B;
    font-size: 13px;
    text-align: center;
    vertical-align: middle;
}

tr:hover {
    background: #F8FAFC;
}

/* Input Fields */
td input, td textarea {
    width: 100%;
    padding: 8px 10px;
    border: 2px solid #E2E8F0;
    border-radius: 10px;
    outline: none;
    background: white;
    color: #1E293B;
    font-size: 12px;
    transition: all 0.3s;
}

td input:focus, td textarea:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

td input[readonly], td textarea[readonly] {
    background: #F8FAFC;
    color: #64748B;
    border-color: #E2E8F0;
}

td textarea {
    resize: vertical;
    min-height: 40px;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 5px;
    flex-direction: column;
}

.btn {
    padding: 8px 12px;
    border: none;
    border-radius: 30px;
    font-weight: 600;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    width: 100%;
}

.btn-update {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
}

.btn-update:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(16, 185, 129, 0.4);
}

.btn-delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
}

.btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(239, 68, 68, 0.4);
}

/* Status Badge */
.status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 40px;
    font-size: 11px;
    font-weight: 600;
    text-align: center;
    min-width: 60px;
}

.status-active {
    background: #d1fae5;
    color: #059669;
    border: 1px solid #10b981;
}

.status-inactive {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #ef4444;
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
    margin-bottom: 20px;
}

.empty-state a {
    display: inline-block;
    padding: 12px 25px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 40px;
    font-weight: 600;
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
        padding: 20px;
    }
    
    .bank-info-bar {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .stats-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-box {
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
        <a href="Viewaccount.php" class="tab-link active">
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
        
        <!-- Page Header -->
        <div class="page-header">
            <h2>
                <i class="fa fa-users"></i>
                <?php echo htmlspecialchars($bank_name); ?> - Customer Accounts
            </h2>
            <p>Manage and update customer account information for <?php echo htmlspecialchars($bank_name); ?></p>
        </div>
        
        <!-- Stats and Search -->
        <div class="stats-row">
            <div class="stat-box">
                <i class="fa fa-users"></i>
                <span>Total Accounts: <strong><?php echo $row_count; ?></strong></span>
            </div>
            
            <div class="stat-box">
                <i class="fa fa-building-columns"></i>
                <span>Bank: <strong><?php echo htmlspecialchars($bank_name); ?></strong></span>
            </div>
            
            <div class="search-box">
                <i class="fa fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search accounts..." onkeyup="searchTable()">
            </div>
        </div>
        
        <!-- Table Container -->
        <div class="table-container">
            <table id="accountsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Aadhar</th>
                        <th>Gender</th>
                        <th>Balance</th>
                        <th>Password</th>
                        <th>Account No</th>
                        <th>PIN</th>
                        <th>Max Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($row_count > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <form method="post">
                                <td>
                                    <input type="text" name="s1" value="<?php echo htmlspecialchars($row['name']); ?>">
                                </td>
                                <td>
                                    <textarea name="s2"><?php echo htmlspecialchars($row['address']); ?></textarea>
                                </td>
                                <td>
                                    <input type="email" name="s3" value="<?php echo htmlspecialchars($row['email']); ?>">
                                </td>
                                <td>
                                    <input type="text" name="s4" value="<?php echo htmlspecialchars($row['phone']); ?>">
                                </td>
                                <td>
                                    <input type="text" name="s5" value="<?php echo htmlspecialchars($row['adharno']); ?>" readonly>
                                </td>
                                <td>
                                    <input type="text" name="s6" value="<?php echo htmlspecialchars($row['gender']); ?>" readonly>
                                </td>
                                <td>
                                    <input type="text" name="s7" value="<?php echo htmlspecialchars($row['balance']); ?>" readonly>
                                </td>
                                <td>
                                    <input type="text" name="s8" value="<?php echo htmlspecialchars($row['password']); ?>" readonly>
                                </td>
                                <td>
                                    <input type="text" name="s9" value="<?php echo htmlspecialchars($row['accno']); ?>" readonly>
                                </td>
                                <td>
                                    <input type="text" name="s10" value="<?php echo htmlspecialchars($row['pin']); ?>" readonly>
                                </td>
                                <td>
                                    <input type="text" name="s11" value="<?php echo htmlspecialchars($row['maxamount']); ?>" readonly>
                                </td>
                                <td>
                                    <?php 
                                    $status = $row['status'] ?? 'active';
                                    $statusClass = ($status == 'active') ? 'status-badge status-active' : 'status-badge status-inactive';
                                    ?>
                                    <span class="<?php echo $statusClass; ?>">
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                    <input type="hidden" name="s12" value="<?php echo $status; ?>">
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="submit" name="Update" class="btn btn-update" onclick="return confirm('Update this account?')">
                                            <i class="fa fa-save"></i> Update
                                        </button>
                                        <button type="submit" name="Delete" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this account? This action cannot be undone.')">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </form>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="13">
                                <div class="empty-state">
                                    <i class="fa fa-users-slash"></i>
                                    <h3>No Accounts Found for <?php echo htmlspecialchars($bank_name); ?></h3>
                                    <p>Start by adding a new customer account for <?php echo htmlspecialchars($bank_name); ?></p>
                                    <a href="addaccount.php">
                                        <i class="fa fa-user-plus"></i> Add New Account
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
/* UPDATE */
if(isset($_POST['Update'])){
    // Check if all required fields are present
    if(isset($_POST['s1'], $_POST['s2'], $_POST['s3'], $_POST['s4'], $_POST['s8'], $_POST['s9'])) {
        
        $s1 = mysqli_real_escape_string($conn, $_POST['s1']);
        $s2 = mysqli_real_escape_string($conn, $_POST['s2']);
        $s3 = mysqli_real_escape_string($conn, $_POST['s3']);
        $s4 = mysqli_real_escape_string($conn, $_POST['s4']);
        $s8 = mysqli_real_escape_string($conn, $_POST['s8']);
        $s9 = mysqli_real_escape_string($conn, $_POST['s9']);

        // Make sure we only update accounts belonging to this bank
        $update = "UPDATE account SET 
            name='$s1',
            address='$s2',
            email='$s3',
            phone='$s4',
            password='$s8'
            WHERE accno='$s9' AND bank_name='$bank_name'";

        if(mysqli_query($conn, $update)){
            // Check if any row was actually updated
            if(mysqli_affected_rows($conn) > 0) {
                echo "<script>alert('✅ Account Updated Successfully');window.location='Viewaccount.php';</script>";
            } else {
                echo "<script>alert('❌ No changes made or account not found');</script>";
            }
        } else {
            echo "<script>alert('❌ Error: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('❌ Missing required fields for update');</script>";
    }
}

/* DELETE */
if(isset($_POST['Delete'])){
    if(isset($_POST['s9'])) {
        $s9 = mysqli_real_escape_string($conn, $_POST['s9']);
        
        // Make sure we only delete accounts belonging to this bank
        $delete_query = "DELETE FROM account WHERE accno='$s9' AND bank_name='$bank_name'";
        if(mysqli_query($conn, $delete_query)){
            // Check if any row was actually deleted
            if(mysqli_affected_rows($conn) > 0) {
                echo "<script>alert('✅ Account Deleted Successfully');window.location='Viewaccount.php';</script>";
            } else {
                echo "<script>alert('❌ Account not found or not authorized to delete');</script>";
            }
        } else {
            echo "<script>alert('❌ Error: " . mysqli_error($conn) . "');</script>";
        }
    }
}

// Display any database errors for debugging (will show in browser console)
if (mysqli_error($conn)) {
    echo "<script>console.log('Database Error: " . mysqli_error($conn) . "');</script>";
}
?>

<script>
// Search functionality
function searchTable() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let rows = document.querySelectorAll("#accountsTable tbody tr");
    
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
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
</script>

</body>
</html>
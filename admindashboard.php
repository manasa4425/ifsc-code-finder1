<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}

// Include database connection for real stats
include 'db.php';

// For showing admin name in dashboard
$admin_name = $_SESSION['admin'] ?? 'Administrator';

// Get actual counts from database
$total_banks = 0;
$total_customers = 0;

$bank_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM bank");
if ($bank_query) {
    $total_banks = mysqli_fetch_assoc($bank_query)['count'];
}

$customer_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM account");
if ($customer_query) {
    $total_customers = mysqli_fetch_assoc($customer_query)['count'];
}

// For transactions count (if you have a transactions table)
$total_transactions = rand(1000, 9999); // Placeholder - replace with actual query
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | Bank IFSC Finder</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body{
  font-family: 'Segoe UI', Arial, sans-serif;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
  position: relative;
  overflow-x: hidden;
}

/* Background Pattern - Matching login pages */
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

/* Welcome Banner - FIXED SPACING */
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

.welcome-banner .admin-name {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
    margin: 0 2px;
}

/* Status dot and text container */
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
    background: none !important;
    -webkit-text-fill-color: #10B981 !important;
    color: #10B981;
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 0.3px;
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

/* Dashboard Card */
.dashboard-card{
  width: 1100px;
  max-width: 100%;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 30px;
  padding: 50px 40px;
  box-shadow: 0 25px 60px rgba(0,0,0,0.3);
  border: 1px solid rgba(255,255,255,0.2);
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

/* Header */
.header{
  text-align: center;
  color: #1E293B;
  margin-bottom: 45px;
  position: relative;
}

.header h1{
  font-size: 38px;
  font-weight: 700;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
}

.header h1 i {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-size: 42px;
}

.header p{
  color: #64748B;
  font-size: 16px;
  letter-spacing: 0.5px;
  position: relative;
  display: inline-block;
  padding: 0 20px;
}

.header p::before,
.header p::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 30px;
    height: 2px;
    background: linear-gradient(90deg, transparent, #667eea, transparent);
}

.header p::before {
    left: -30px;
}

.header p::after {
    right: -30px;
}

/* Stats Row */
.stats-row {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

.stat-box {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 20px 35px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    border: 1px solid #E2E8F0;
    transition: all 0.3s ease;
    min-width: 200px;
}

.stat-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
    border-color: #667eea;
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon i {
    color: white;
    font-size: 24px;
}

.stat-info h4 {
    color: #64748B;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 5px;
}

.stat-info .stat-number {
    color: #1E293B;
    font-size: 24px;
    font-weight: 700;
}

/* Grid */
.grid{
  display: flex;
  justify-content: center;
  gap: 30px;
  flex-wrap: wrap;
  margin-top: 20px;
}

/* Cards */
.card{
  width: 280px;
  background: white;
  border-radius: 24px;
  padding: 35px 25px;
  text-align: center;
  color: #1E293B;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  cursor: pointer;
  text-decoration: none;
  border: 2px solid #E2E8F0;
  position: relative;
  overflow: hidden;
  box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
}

.card::before {
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

.card:hover::before {
    transform: scaleX(1);
}

.card:hover{
  transform: translateY(-12px) scale(1.02);
  border-color: #667eea;
  box-shadow: 0 20px 40px -10px rgba(102, 126, 234, 0.4);
}

.card i{
  font-size: 48px;
  margin-bottom: 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  transition: all 0.3s ease;
}

.card:hover i {
    transform: scale(1.1) rotate(5deg);
}

.card h3{
  font-size: 22px;
  font-weight: 700;
  margin-bottom: 10px;
  color: #1E293B;
}

.card p{
  font-size: 14px;
  color: #64748B;
  line-height: 1.6;
}

/* Card-specific hover effects */
.card:nth-child(1):hover {
    box-shadow: 0 20px 40px -10px rgba(34, 197, 94, 0.4);
}
.card:nth-child(2):hover {
    box-shadow: 0 20px 40px -10px rgba(59, 130, 246, 0.4);
}
.card:nth-child(3):hover {
    box-shadow: 0 20px 40px -10px rgba(239, 68, 68, 0.4);
}

/* Quick Actions */
.quick-actions {
    margin-top: 40px;
    text-align: center;
}

.quick-actions h4 {
    color: #64748B;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 15px;
    position: relative;
    display: inline-block;
    padding: 0 20px;
}

.quick-actions h4::before,
.quick-actions h4::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 40px;
    height: 1px;
    background: linear-gradient(90deg, transparent, #94A3B8, transparent);
}

.quick-actions h4::before {
    left: -40px;
}

.quick-actions h4::after {
    right: -40px;
}

.action-badges {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}

.action-badge {
    background: #F8FAFC;
    padding: 8px 20px;
    border-radius: 30px;
    color: #64748B;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #E2E8F0;
    transition: all 0.3s ease;
    cursor: default;
}

.action-badge i {
    color: #667eea;
    font-size: 12px;
}

.action-badge:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateY(-2px);
    border-color: transparent;
}

.action-badge:hover i {
    color: white;
}

/* Footer Note - Matching login pages */
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
@media(max-width: 768px){
  .dashboard-card{
    padding: 30px 20px;
    margin-top: 60px;
  }
  
  .header h1{
    font-size: 28px;
  }
  
  .header h1 i {
    font-size: 32px;
  }
  
  .header p::before,
  .header p::after {
    display: none;
  }
  
  .card{
    width: 100%;
    max-width: 320px;
  }
  
  .stat-box {
    width: 100%;
    justify-content: center;
  }
  
  .welcome-banner {
    width: 95%;
    font-size: 13px;
    padding: 10px 15px;
    white-space: normal;
    flex-wrap: wrap;
    justify-content: center;
  }
  
  .status-container {
    padding: 3px 8px;
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

/* Loading Animation */
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.05); }
}

.loading {
    animation: pulse 2s infinite;
}
</style>
</head>
<body>

<!-- Background Pattern -->
<div class="background-pattern"></div>

<!-- Welcome Banner - FIXED SPACING -->
<div class="welcome-banner">
    <i class="fas fa-user-shield"></i>
    <span>Welcome back,</span>
    <span class="admin-name"><?php echo htmlspecialchars($admin_name); ?></span>
    
    <div class="status-container">
        <span class="status-dot"></span>
        <span class="status-text">SESSION ACTIVE</span>
    </div>
</div>

<!-- Main Dashboard Card -->
<div class="dashboard-card">
  <div class="header">
    <h1>
      <i class="fas fa-chart-line"></i> 
      Admin Dashboard
    </h1>
    <p>Manage Banks • View Details • Secure Access</p>
  </div>

  <!-- Stats Row - with real data -->
  <div class="stats-row">
    <div class="stat-box">
      <div class="stat-icon">
        <i class="fas fa-university"></i>
      </div>
      <div class="stat-info">
        <h4>Total Banks</h4>
        <div class="stat-number"><?php echo $total_banks; ?></div>
      </div>
    </div>
    
    <div class="stat-box">
      <div class="stat-icon">
        <i class="fas fa-users"></i>
      </div>
      <div class="stat-info">
        <h4>Total Customers</h4>
        <div class="stat-number"><?php echo $total_customers; ?></div>
      </div>
    </div>
    
    <div class="stat-box">
      <div class="stat-icon">
        <i class="fas fa-exchange-alt"></i>
      </div>
      <div class="stat-info">
        <h4>Transactions</h4>
        <div class="stat-number"><?php echo number_format($total_transactions); ?></div>
      </div>
    </div>
  </div>

  <!-- Main Action Cards -->
  <div class="grid">
    <a href="addbank.php" class="card">
      <i class="fa fa-plus-circle"></i>
      <h3>Add New Bank</h3>
      <p>Create new bank records with complete details and IFSC codes</p>
    </a>

    <a href="ViewBank.php" class="card">
      <i class="fa fa-eye"></i>
      <h3>View Banks</h3>
      <p>Browse, edit or delete bank information securely</p>
    </a>

    <a href="logout1.php" class="card">
      <i class="fa fa-sign-out-alt"></i>
      <h3>Logout</h3>
      <p>Safely exit the admin portal and clear session</p>
    </a>
  </div>

  <!-- Quick Actions -->
  <div class="quick-actions">
    <h4>Quick Actions</h4>
    <div class="action-badges">
      <span class="action-badge"><i class="fas fa-download"></i> Export Data</span>
      <span class="action-badge"><i class="fas fa-print"></i> Print Reports</span>
      <span class="action-badge"><i class="fas fa-bell"></i> Notifications</span>
      <span class="action-badge"><i class="fas fa-shield-alt"></i> Security Check</span>
    </div>
  </div>
</div>

<!-- Live Date -->
<div class="live-date" id="live-date">
    <i class="fas fa-calendar-alt"></i> Loading...
</div>

<!-- Footer Note -->
<div class="footer-note">
    <i class="fas fa-shield-alt"></i> Admin Access Only • Protected Area
</div>

<script>
// Function to update date
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

// Add smooth scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});
</script>

</body>
</html>
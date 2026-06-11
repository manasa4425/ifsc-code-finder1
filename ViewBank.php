<?php
include "db.php";
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}

$admin_name = $_SESSION['admin'] ?? 'Administrator';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Banks | Bank IFSC Finder</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

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
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

.welcome-banner .admin-name {
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
    gap: 15px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.tab-link {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    text-decoration: none;
    padding: 14px 32px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(5px);
}

.tab-link i {
    font-size: 18px;
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
    padding: 40px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.card-header {
    text-align: center;
    margin-bottom: 35px;
}

.card-header h2 {
    color: #1E293B;
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.card-header h2 i {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 36px;
}

.card-header p {
    color: #64748B;
    font-size: 15px;
}

/* Stats Summary */
.stats-summary {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.stat-item {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 15px 25px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 15px;
    border: 1px solid #E2E8F0;
}

.stat-icon {
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

.stat-info h4 {
    color: #64748B;
    font-size: 13px;
    font-weight: 500;
}

.stat-info .stat-number {
    color: #1E293B;
    font-size: 22px;
    font-weight: 700;
}

/* Search Section */
.search-section {
    display: flex;
    gap: 15px;
    margin-bottom: 25px;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
}

.search-box {
    position: relative;
    flex: 1;
    min-width: 300px;
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
    padding: 14px 14px 14px 45px;
    border: 2px solid #E2E8F0;
    border-radius: 50px;
    outline: none;
    background: white;
    color: #1E293B;
    font-size: 15px;
    transition: all 0.3s;
}

.search-box input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}

.export-btn {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    color: white;
    border: none;
    padding: 14px 25px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.export-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
}

/* Table Container */
.table-container {
    overflow-x: auto;
    border-radius: 20px;
    background: white;
    border: 1px solid #E2E8F0;
    margin-top: 20px;
}

.banks-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1400px;
}

.banks-table th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 16px 12px;
    font-weight: 600;
    font-size: 14px;
    text-align: left;
    white-space: nowrap;
}

.banks-table td {
    padding: 16px 12px;
    border-bottom: 1px solid #E2E8F0;
    color: #1E293B;
    font-size: 14px;
    vertical-align: middle;
}

.banks-table tr:hover {
    background: #F8FAFC;
}

.banks-table tr:last-child td {
    border-bottom: none;
}

/* Input Fields in Table */
.banks-table input[type="text"],
.banks-table input[type="password"],
.banks-table textarea {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #E2E8F0;
    border-radius: 10px;
    outline: none;
    background: white;
    color: #1E293B;
    font-size: 13px;
    transition: all 0.3s;
}

.banks-table input:focus,
.banks-table textarea:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.banks-table textarea {
    min-height: 60px;
    resize: vertical;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 8px;
    flex-direction: column;
}

.btn {
    padding: 10px 12px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
}

.btn-update {
    background: #EFF6FF;
    color: #2563EB;
    border: 1px solid #2563EB;
}

.btn-update:hover {
    background: #2563EB;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
}

.btn-delete {
    background: #FEF2F2;
    color: #DC2626;
    border: 1px solid #DC2626;
}

.btn-delete:hover {
    background: #DC2626;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 38, 38, 0.3);
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
    
    .search-section {
        flex-direction: column;
    }
    
    .search-box {
        width: 100%;
    }
    
    .export-btn {
        width: 100%;
        justify-content: center;
    }
    
    .stats-summary {
        flex-direction: column;
    }
    
    .stat-item {
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
    <i class="fas fa-user-shield"></i>
    <span>Welcome back,</span>
    <span class="admin-name"><?php echo htmlspecialchars($admin_name); ?></span>
    
    <div class="status-container">
        <span class="status-dot"></span>
        <span class="status-text">SESSION ACTIVE</span>
    </div>
</div>

<!-- Main Container -->
<div class="main-container">
    
    <!-- Navigation Tabs -->
    <div class="nav-tabs">
        <a href="addbank.php" class="tab-link">
            <i class="fa fa-plus-circle"></i> Add Bank
        </a>
        <a href="ViewBank.php" class="tab-link active">
            <i class="fa fa-eye"></i> View Banks
        </a>
        <a href="logout1.php" class="tab-link logout">
            <i class="fa fa-sign-out-alt"></i> Logout
        </a>
    </div>
    
    <!-- Content Card -->
    <div class="content-card">
        <div class="card-header">
            <h2>
                <i class="fa fa-building-columns"></i>
                Manage Banks
            </h2>
            <p>View, update or delete bank information</p>
        </div>
        
        <?php
        $sql = "SELECT * FROM bank ORDER BY id ASC";
        $result = mysqli_query($conn, $sql);
        $row_count = mysqli_num_rows($result);
        ?>
        
        <!-- Stats Summary -->
        <div class="stats-summary">
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-university"></i>
                </div>
                <div class="stat-info">
                    <h4>Total Banks</h4>
                    <div class="stat-number"><?php echo $row_count; ?></div>
                </div>
            </div>
            
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-city"></i>
                </div>
                <div class="stat-info">
                    <h4>Cities Covered</h4>
                    <div class="stat-number">12</div>
                </div>
            </div>
            
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="stat-info">
                    <h4>Active Branches</h4>
                    <div class="stat-number"><?php echo $row_count; ?></div>
                </div>
            </div>
        </div>
        
        <!-- Search Section -->
        <div class="search-section">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search by bank name, IFSC code, city..." onkeyup="searchTable()">
            </div>
            <button class="export-btn" onclick="exportTable()">
                <i class="fas fa-download"></i> Export to CSV
            </button>
        </div>
        
        <!-- Table -->
        <div class="table-container">
            <table class="banks-table" id="banksTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Bank Name</th>
                        <th>Branch</th>
                        <th>State</th>
                        <th>City</th>
                        <th>District</th>
                        <th>Address</th>
                        <th>IFSC Code</th>
                        <th>Phone</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($row_count > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <form method="post">
                                <td>
                                    <input type="text" name="id" value="<?php echo $row['id']; ?>" readonly style="background:#F1F5F9;">
                                </td>
                                <td>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                                </td>
                                <td>
                                    <input type="text" name="branch" value="<?php echo htmlspecialchars($row['branch']); ?>" required>
                                </td>
                                <td>
                                    <input type="text" name="state" value="<?php echo htmlspecialchars($row['state']); ?>" required>
                                </td>
                                <td>
                                    <input type="text" name="city" value="<?php echo htmlspecialchars($row['city']); ?>" required>
                                </td>
                                <td>
                                    <input type="text" name="district" value="<?php echo htmlspecialchars($row['district']); ?>" required>
                                </td>
                                <td>
                                    <textarea name="address" required><?php echo htmlspecialchars($row['address']); ?></textarea>
                                </td>
                                <td>
                                    <input type="text" name="ifsc" value="<?php echo htmlspecialchars($row['ifsc']); ?>" required>
                                </td>
                                <td>
                                    <input type="text" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>" required>
                                </td>
                                <td>
                                    <input type="text" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
                                </td>
                                <td>
                                    <input type="password" name="password" value="<?php echo htmlspecialchars($row['password']); ?>" required>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="submit" name="Update" class="btn btn-update" onclick="return confirm('Update this bank?')">
                                            <i class="fas fa-save"></i> Update
                                        </button>
                                        <button type="submit" name="Delete" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this bank? This action cannot be undone.')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </form>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12">
                                <div class="empty-state">
                                    <i class="fas fa-building"></i>
                                    <h3>No Banks Found</h3>
                                    <p>Get started by adding a new bank</p>
                                    <a href="addbank.php" style="display: inline-block; margin-top: 15px; padding: 10px 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 50px;">Add New Bank</a>
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
    <i class="fas fa-shield-alt"></i> Admin Access Only • Protected Area
</div>

<?php
/* UPDATE */
if(isset($_POST['Update'])){
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $branch = mysqli_real_escape_string($conn, $_POST['branch']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $ifsc = mysqli_real_escape_string($conn, $_POST['ifsc']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $update = "UPDATE bank SET 
        name='$name', 
        branch='$branch', 
        state='$state', 
        city='$city',
        district='$district', 
        address='$address', 
        ifsc='$ifsc',
        phone='$phone', 
        username='$username', 
        password='$password'
        WHERE id='$id'";

    if(mysqli_query($conn, $update)){
        echo "<script>alert('Bank updated successfully!');window.location='ViewBank.php';</script>";
    } else {
        echo "<script>alert('Error updating bank: " . mysqli_error($conn) . "');</script>";
    }
}

/* DELETE */
if(isset($_POST['Delete'])){
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    mysqli_query($conn, "DELETE FROM bank WHERE id='$id'");
    
    // Reset AUTO_INCREMENT
    mysqli_query($conn, "SET @count = 0");
    mysqli_query($conn, "UPDATE bank SET id = @count:= @count + 1");
    mysqli_query($conn, "ALTER TABLE bank AUTO_INCREMENT = 1");

    echo "<script>alert('Bank deleted successfully!');window.location='ViewBank.php';</script>";
}
?>

<script>
// Search functionality
function searchTable() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let rows = document.querySelectorAll("#banksTable tbody tr");
    
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

// Export to CSV
function exportTable() {
    let csv = [];
    let rows = document.querySelectorAll("#banksTable tr");
    
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (let j = 0; j < cols.length - 1; j++) { // Exclude action column
            let data = cols[j].innerText.replace(/,/g, ' '); // Remove commas to avoid CSV issues
            row.push(data);
        }
        csv.push(row.join(','));
    }
    
    let csvFile = new Blob([csv.join('\n')], {type: 'text/csv'});
    let downloadLink = document.createElement('a');
    downloadLink.download = 'banks_list.csv';
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>

</body>
</html>
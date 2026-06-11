<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}

include "db.php";

// Get admin name for welcome banner
$admin_name = $_SESSION['admin'] ?? 'Administrator';

/* Fetch bank names for suggestion dropdown */
$banks = [];
$res = mysqli_query($conn, "SELECT name, ifsc FROM bank");
if (!$res) {
    die("Database query failed: " . mysqli_error($conn));
}
while ($row = mysqli_fetch_assoc($res)) {
    $banks[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add New Bank | Bank IFSC Finder</title>
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

/* Background Pattern - Matching dashboard */
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

/* Welcome Banner - Matching dashboard */
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

/* Form Card */
.form-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 30px;
    padding: 40px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.form-header {
    text-align: center;
    margin-bottom: 35px;
}

.form-header h2 {
    color: #1E293B;
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.form-header h2 i {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 36px;
}

.form-header p {
    color: #64748B;
    font-size: 15px;
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.full-width {
    grid-column: span 2;
}

/* Input Groups */
.input-group {
    margin-bottom: 5px;
    position: relative;
}

label {
    display: block;
    color: #1E293B;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 8px;
}

label i {
    color: #667eea;
    margin-right: 6px;
    font-size: 13px;
}

.input-wrapper {
    position: relative;
}

.input-wrapper i {
    position: absolute;
    top: 50%;
    left: 15px;
    transform: translateY(-50%);
    color: #667eea;
    font-size: 16px;
    z-index: 1;
}

input, select {
    width: 100%;
    padding: 14px 14px 14px 45px;
    border: 2px solid #E2E8F0;
    border-radius: 16px;
    outline: none;
    background: white;
    color: #1E293B;
    font-size: 15px;
    transition: all 0.3s;
}

input:focus, select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
}

input:focus + i {
    color: #764ba2;
}

input::placeholder {
    color: #94A3B8;
}

/* Arrow for suggestion */
.arrow {
    position: absolute;
    right: 15px;
    top: 42px;
    color: #667eea;
    cursor: pointer;
    font-size: 14px;
    background: #F8FAFC;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    transition: all 0.3s;
}

.arrow:hover {
    background: #667eea;
    color: white;
}

/* Suggestions dropdown */
.suggestions {
    position: absolute;
    width: 100%;
    background: white;
    border-radius: 16px;
    margin-top: 5px;
    display: none;
    max-height: 200px;
    overflow: auto;
    z-index: 99;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    border: 1px solid #E2E8F0;
}

.suggestions div {
    padding: 12px 15px;
    cursor: pointer;
    color: #1E293B;
    transition: all 0.3s;
    border-bottom: 1px solid #F1F5F9;
}

.suggestions div:last-child {
    border-bottom: none;
}

.suggestions div:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    padding: 16px;
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
    gap: 12px;
    margin-top: 30px;
    position: relative;
    overflow: hidden;
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
    font-size: 20px;
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
    
    .full-width {
        grid-column: span 1;
    }
    
    .form-card {
        padding: 25px;
    }
    
    .tab-link {
        padding: 10px 20px;
        font-size: 14px;
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
        <a href="addbank.php" class="tab-link active">
            <i class="fa fa-plus-circle"></i> Add Bank
        </a>
        <a href="ViewBank.php" class="tab-link">
            <i class="fa fa-eye"></i> View Banks
        </a>
        <a href="logout1.php" class="tab-link logout">
            <i class="fa fa-sign-out-alt"></i> Logout
        </a>
    </div>
    
    <!-- Form Card -->
    <div class="form-card">
        <div class="form-header">
            <h2>
                <i class="fa fa-university"></i>
                Add New Bank
            </h2>
            <p>Fill in the details to register a new bank branch</p>
        </div>
        
        <form method="POST" action="insertbank.php">
            <div class="form-grid">
                <!-- Bank Name with Suggestion -->
                <div class="input-group full-width">
                    <label><i class="fa fa-building"></i> Bank Name</label>
                    <div class="input-wrapper">
                        <i class="fa fa-search"></i>
                        <input type="text" id="bankname" name="s1" required autocomplete="off" placeholder="Enter or select bank name">
                        <span class="arrow" onclick="toggleList()">▼</span>
                    </div>
                    <div class="suggestions" id="suggestions"></div>
                </div>
                
                <!-- Branch Name -->
                <div class="input-group">
                    <label><i class="fa fa-code-branch"></i> Branch Name</label>
                    <div class="input-wrapper">
                        <i class="fa fa-map-pin"></i>
                        <input type="text" name="s7" required placeholder="e.g., Main Branch">
                    </div>
                </div>
                
                <!-- IFSC Code -->
                <div class="input-group">
                    <label><i class="fa fa-qrcode"></i> IFSC Code</label>
                    <div class="input-wrapper">
                        <i class="fa fa-barcode"></i>
                        <input type="text" id="ifsc" name="s3" required pattern="[A-Z]{4}0[A-Z0-9]{6}" title="Enter valid IFSC code" placeholder="e.g., SBIN0001234">
                    </div>
                </div>
                
                <!-- Address -->
                <div class="input-group full-width">
                    <label><i class="fa fa-location-dot"></i> Address</label>
                    <div class="input-wrapper">
                        <i class="fa fa-map"></i>
                        <input type="text" name="s2" required placeholder="Complete street address">
                    </div>
                </div>
                
                <!-- City -->
                <div class="input-group">
                    <label><i class="fa fa-city"></i> City</label>
                    <div class="input-wrapper">
                        <i class="fa fa-building"></i>
                        <input type="text" name="s9" required placeholder="e.g., Mumbai">
                    </div>
                </div>
                
                <!-- District -->
                <div class="input-group">
                    <label><i class="fa fa-map"></i> District</label>
                    <div class="input-wrapper">
                        <i class="fa fa-location-dot"></i>
                        <input type="text" name="s10" required placeholder="e.g., Mumbai City">
                    </div>
                </div>
                
                <!-- State -->
                <div class="input-group">
                    <label><i class="fa fa-flag"></i> State</label>
                    <div class="input-wrapper">
                        <i class="fa fa-map-pin"></i>
                        <input type="text" name="s8" required placeholder="e.g., Maharashtra">
                    </div>
                </div>
                
                <!-- Phone -->
                <div class="input-group">
                    <label><i class="fa fa-phone"></i> Phone</label>
                    <div class="input-wrapper">
                        <i class="fa fa-phone-alt"></i>
                        <input type="text" name="s4" required pattern="[0-9]{10}" title="Enter 10-digit phone number" placeholder="10-digit number">
                    </div>
                </div>
                
                <!-- Username -->
                <div class="input-group">
                    <label><i class="fa fa-user"></i> Username</label>
                    <div class="input-wrapper">
                        <i class="fa fa-user-circle"></i>
                        <input type="text" name="s5" required placeholder="Login username">
                    </div>
                </div>
                
                <!-- Password -->
                <div class="input-group">
                    <label><i class="fa fa-lock"></i> Password</label>
                    <div class="input-wrapper">
                        <i class="fa fa-key"></i>
                        <input type="password" name="s6" required placeholder="••••••••">
                    </div>
                </div>
            </div>
            
            <button type="submit" class="submit-btn">
                <i class="fa fa-plus-circle"></i> Create Bank
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
    <i class="fas fa-shield-alt"></i> Admin Access Only • Protected Area
</div>

<script>
let banks = <?php echo json_encode($banks); ?>;
let input = document.getElementById("bankname");
let ifsc = document.getElementById("ifsc");
let sug = document.getElementById("suggestions");

/* Show dropdown */
function toggleList(){
    sug.innerHTML = "";
    banks.forEach(b => {
        let d = document.createElement("div");
        d.innerText = b.name;
        d.onclick = () => {
            input.value = b.name;
            ifsc.value = b.ifsc;
            sug.style.display = "none";
        }
        sug.appendChild(d);
    });
    sug.style.display = "block";
}

/* Search filter */
input.addEventListener("input", function(){
    let val = input.value.toLowerCase();
    sug.innerHTML = "";
    
    if(val){
        let matches = banks.filter(b => b.name.toLowerCase().includes(val));
        matches.forEach(b => {
            let d = document.createElement("div");
            d.innerText = b.name;
            d.onclick = () => {
                input.value = b.name;
                ifsc.value = b.ifsc;
                sug.style.display = "none";
            }
            sug.appendChild(d);
        });
        sug.style.display = matches.length ? "block" : "none";
    } else {
        sug.style.display = "none";
    }
});

/* Uppercase IFSC */
ifsc.addEventListener("input", function(){
    this.value = this.value.toUpperCase();
});

/* Live Date */
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

// Close suggestions when clicking outside
document.addEventListener("click", function(e) {
    if (!input.contains(e.target) && !sug.contains(e.target) && !e.target.classList.contains('arrow')) {
        sug.style.display = "none";
    }
});
</script>

</body>
</html>
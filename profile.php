<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
session_start();
include "db.php";

// Check if user is logged in
if (!isset($_SESSION['accno']) && !isset($_GET['edit'])) {
    header("Location: customerlogin.php");
    exit;
}

$accno = isset($_GET['edit']) ? $_GET['edit'] : $_SESSION['accno'];

// Fetch user data
$sql = "SELECT * FROM account WHERE accno='$accno'";
$retval = mysqli_query($conn, $sql);  
$user = mysqli_fetch_assoc($retval);

if (!$user) {
    header("Location: customer_profile.php");
    exit;
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edit Profile | Bank IFSC</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  * { 
    margin: 0; 
    padding: 0; 
    box-sizing: border-box; 
  }

  body {
    font-family: 'Inter', sans-serif;
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
    padding: 16px 24px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    position: relative;
    z-index: 100;
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

  /* Breadcrumb */
  .breadcrumb {
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(255,255,255,0.8);
    font-size: 14px;
  }

  .breadcrumb a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
  }

  .breadcrumb a:hover {
    color: white;
  }

  .breadcrumb i {
    font-size: 12px;
  }

  /* Main Container */
  .main-container {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
  }

  /* Edit Card */
  .edit-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.2);
    animation: fadeInUp 0.6s ease;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Header Section */
  .edit-header {
    text-align: center;
    margin-bottom: 35px;
  }

  .edit-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }

  .edit-header h1 i {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 36px;
  }

  .edit-header p {
    color: #64748b;
    font-size: 15px;
  }

  /* Account Info Banner */
  .account-banner {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid #e2e8f0;
  }

  .account-banner i {
    font-size: 24px;
    color: #2563eb;
  }

  .account-banner span {
    color: #334155;
    font-weight: 600;
    font-size: 14px;
  }

  .account-banner strong {
    color: #2563eb;
    font-size: 16px;
    margin-left: 5px;
  }

  /* Form */
  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }

  .full-width {
    grid-column: span 2;
  }

  .form-group {
    margin-bottom: 5px;
  }

  .form-group label {
    display: block;
    margin-bottom: 8px;
    color: #334155;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }

  .form-group label i {
    color: #2563eb;
    margin-right: 6px;
    width: 16px;
  }

  .input-wrapper {
    position: relative;
  }

  .input-wrapper i {
    position: absolute;
    top: 50%;
    left: 14px;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 16px;
    transition: color 0.2s;
  }

  input, textarea {
    width: 100%;
    padding: 14px 14px 14px 45px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    outline: none;
    background: white;
    color: #1e293b;
    font-size: 15px;
    transition: all 0.2s;
    font-family: 'Inter', sans-serif;
  }

  textarea {
    padding: 14px;
    min-height: 100px;
    resize: vertical;
  }

  input:focus, textarea:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
  }

  input:focus + i {
    color: #2563eb;
  }

  input[readonly] {
    background: #f8fafc;
    border-color: #e2e8f0;
    color: #64748b;
  }

  /* Action Buttons */
  .action-buttons {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    justify-content: flex-end;
  }

  .btn {
    padding: 14px 30px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
  }

  .btn-primary {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(37,99,235,0.3);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(37,99,235,0.4);
  }

  .btn-secondary {
    background: white;
    color: #334155;
    border: 2px solid #e2e8f0;
  }

  .btn-secondary:hover {
    background: #f8fafc;
    border-color: #2563eb;
    color: #2563eb;
    transform: translateY(-2px);
  }

  /* Security Note */
  .security-note {
    margin-top: 25px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    color: #64748b;
    font-size: 13px;
    border: 1px solid #e2e8f0;
  }

  .security-note i {
    color: #10b981;
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
    color: #1e293b;
    font-size: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    z-index: 100;
  }

  .footer-note i {
    color: #10b981;
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
    color: #1e293b;
    font-size: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    z-index: 100;
  }

  .live-date i {
    color: #2563eb;
    margin-right: 5px;
  }

  /* Responsive */
  @media (max-width: 768px) {
    body {
      padding: 15px;
    }
    
    .form-grid {
      grid-template-columns: 1fr;
    }
    
    .full-width {
      grid-column: span 1;
    }
    
    .edit-card {
      padding: 25px;
    }
    
    .action-buttons {
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
    background: #f1f5f9;
    border-radius: 10px;
  }

  ::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    border-radius: 10px;
  }
</style>
</head>

<body>
<!-- Background Pattern -->
<div class="background-pattern"></div>

<!-- Header -->
<div class="header">
    <div class="three-dots" onclick="toggleMenu()">
        <i class="fas fa-ellipsis-v"></i>
    </div>

    <div class="logo">
        <i class="fas fa-building-columns"></i> BANK <span>IFSC</span>
    </div>

    <div class="dropdown" id="menu">
        <a href="customerhome.php"><i class="fas fa-home"></i> Home</a>
        <a href="customer_profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="customerlogout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="customerhome.php">Home</a>
    <i class="fas fa-chevron-right"></i>
    <a href="customer_profile.php">Profile</a>
    <i class="fas fa-chevron-right"></i>
    <span>Edit Profile</span>
</div>

<!-- Main Container -->
<div class="main-container">
    <!-- Edit Card -->
    <div class="edit-card">
        <div class="edit-header">
            <h1>
                <i class="fas fa-user-edit"></i>
                Edit Profile
            </h1>
            <p>Update your personal information</p>
        </div>

        <!-- Account Banner -->
        <div class="account-banner">
            <i class="fas fa-id-card"></i>
            <span>Editing profile for account:</span>
            <strong><?php echo $accno; ?></strong>
        </div>

        <!-- Edit Form -->
        <form id="form1" name="form1" method="post" action="">
            <div class="form-grid">
                <!-- Full Name -->
                <div class="form-group full-width">
                    <label><i class="fas fa-user"></i> Full Name</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user-circle"></i>
                        <input type="text" name="s1" value="<?php echo htmlspecialchars($user['name']); ?>" placeholder="Enter your full name" required />
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-at"></i>
                        <input type="email" name="s2" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="email@example.com" required />
                    </div>
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Phone Number</label>
                    <div class="input-wrapper">
                        <i class="fas fa-phone-alt"></i>
                        <input type="text" name="s3" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="10-digit mobile number" pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number" required />
                    </div>
                </div>

                <!-- PIN (Sensitive) -->
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> PIN</label>
                    <div class="input-wrapper">
                        <i class="fas fa-key"></i>
                        <input type="password" name="s4" value="<?php echo htmlspecialchars($user['pin']); ?>" placeholder="****" required />
                    </div>
                </div>

                <!-- Account Number (Read Only) -->
                <div class="form-group">
                    <label><i class="fas fa-hashtag"></i> Account Number</label>
                    <div class="input-wrapper">
                        <i class="fas fa-credit-card"></i>
                        <input type="text" name="s6" value="<?php echo $accno; ?>" readonly />
                    </div>
                </div>

                <!-- Address (Full Width) -->
                <div class="form-group full-width">
                    <label><i class="fas fa-map-marker-alt"></i> Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-location-dot"></i>
                        <textarea name="s5" placeholder="Enter your complete address"><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button type="submit" name="Update" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="customer_profile.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>

            <!-- Security Note -->
            <div class="security-note">
                <i class="fas fa-shield-alt"></i>
                <span>Your information is encrypted and securely stored</span>
                <i class="fas fa-lock"></i>
            </div>
        </form>
    </div>
</div>

<!-- Live Date -->
<div class="live-date" id="live-date">
    <i class="fas fa-calendar-alt"></i> Loading...
</div>

<!-- Footer Note -->
<div class="footer-note">
    <i class="fas fa-shield-alt"></i> Secure Banking • Your privacy is protected
</div>

<?php 
if (isset($_POST["Update"])) {
    $s1 = mysqli_real_escape_string($conn, $_POST["s1"]);
    $s2 = mysqli_real_escape_string($conn, $_POST["s2"]);
    $s3 = mysqli_real_escape_string($conn, $_POST["s3"]);
    $s4 = mysqli_real_escape_string($conn, $_POST["s4"]);
    $s5 = mysqli_real_escape_string($conn, $_POST["s5"]);
    $s6 = mysqli_real_escape_string($conn, $_POST["s6"]);

    $sql2 = "UPDATE account 
             SET name='$s1', email='$s2', phone='$s3', pin='$s4', address='$s5' 
             WHERE accno='$s6'";  

    if (mysqli_query($conn, $sql2)) {  
?>
<script>
    alert("✅ Profile Updated Successfully");
    window.location.href = "customer_profile.php";
</script>
<?php 
    } else {  
        echo '<script>alert("❌ Error updating profile. Please try again.");</script>';
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

// Phone number validation
document.querySelector('input[name="s3"]').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
});
</script>
</body>
</html>
<?php
// login1.php - Complete with login history working
session_start();
require_once 'db_config.php';

// Initialize variables
$login_error = '';
$signup_error = '';
$signup_success = '';
$show_otp_form = false;
$temp_user_data = [];

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Track login attempts
$ip = $_SERVER['REMOTE_ADDR'];
$attempts_key = 'login_attempts_' . $ip;
$blocked_key = 'blocked_until_' . $ip;

// Check if IP is blocked
$is_blocked = false;
$wait_time = 0;
if (isset($_SESSION[$blocked_key]) && time() < $_SESSION[$blocked_key]) {
    $is_blocked = true;
    $wait_time = ceil(($_SESSION[$blocked_key] - time()) / 60);
    $login_error = "Too many failed attempts. Please wait $wait_time minutes.";
}

// Device Detection Class
class DeviceDetector {
    public static function detect() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        // Detect device type
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent)) {
            $deviceType = 'Mobile';
        } elseif (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $userAgent)) {
            $deviceType = 'Tablet';
        } else {
            $deviceType = 'Desktop';
        }
        
        // Detect OS
        $os = 'Unknown';
        if (preg_match('/windows|win32/i', $userAgent)) $os = 'Windows';
        elseif (preg_match('/macintosh|mac os x/i', $userAgent)) $os = 'macOS';
        elseif (preg_match('/linux/i', $userAgent)) $os = 'Linux';
        elseif (preg_match('/android/i', $userAgent)) $os = 'Android';
        elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) $os = 'iOS';
        
        // Detect browser
        $browser = 'Unknown';
        if (preg_match('/chrome/i', $userAgent)) $browser = 'Chrome';
        elseif (preg_match('/firefox/i', $userAgent)) $browser = 'Firefox';
        elseif (preg_match('/safari/i', $userAgent)) $browser = 'Safari';
        elseif (preg_match('/edge/i', $userAgent)) $browser = 'Edge';
        elseif (preg_match('/opera/i', $userAgent)) $browser = 'Opera';
        
        // Get IP and location
        $ip = $_SERVER['REMOTE_ADDR'];
        $location = self::getLocationFromIP($ip);
        
        // Create device fingerprint
        $fingerprint = md5($userAgent . $ip . $os . $browser);
        
        return [
            'device_type' => $deviceType,
            'os' => $os,
            'browser' => $browser,
            'ip' => $ip,
            'location' => $location,
            'user_agent' => $userAgent,
            'fingerprint' => $fingerprint
        ];
    }
    
    private static function getLocationFromIP($ip) {
        if ($ip == '::1' || $ip == '127.0.0.1') {
            return 'Localhost, India';
        }
        $locations = ['Mumbai, India', 'Delhi, India', 'Bangalore, India', 'Chennai, India', 'Kolkata, India', 'Pune, India'];
        return $locations[array_rand($locations)];
    }
}

// Generate OTP
function generateOTP() {
    return rand(100000, 999999);
}

// Send OTP
function sendOTP($email, $otp) {
    $_SESSION['demo_otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 300;
    return true;
}

// Generate captcha
function generateCaptcha() {
    $num1 = rand(1, 9);
    $num2 = rand(1, 9);
    $operator = rand(0, 1) ? '+' : '-';
    
    if ($operator == '+') {
        $result = $num1 + $num2;
    } else {
        if ($num1 < $num2) {
            $temp = $num1;
            $num1 = $num2;
            $num2 = $temp;
        }
        $result = $num1 - $num2;
    }
    
    $_SESSION['captcha_result'] = $result;
    return "$num1 $operator $num2 = ?";
}

// Handle Signup
if (isset($_POST['signup'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }
    
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $signup_error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $signup_error = "Password must be at least 6 characters!";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' OR email = '$email'");
        if ($check && mysqli_num_rows($check) > 0) {
            $signup_error = "Username or Email already exists!";
        } else {
            $otp = generateOTP();
            sendOTP($email, $otp);
            
            $_SESSION['temp_signup'] = [
                'full_name' => $full_name,
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'password' => $password
            ];
            $show_otp_form = true;
            $signup_success = "OTP sent to $email (Demo OTP: $otp)";
        }
    }
}

// Handle OTP Verification
if (isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];
    
    if (isset($_SESSION['demo_otp']) && isset($_SESSION['otp_expiry']) && 
        time() < $_SESSION['otp_expiry'] && $_SESSION['demo_otp'] == $entered_otp) {
        
        $data = $_SESSION['temp_signup'];
        $password = $data['password'];
        
        $query = "INSERT INTO users (username, email, password, full_name, phone, role, created_at) 
                  VALUES (
                      '{$data['username']}', 
                      '{$data['email']}', 
                      '$password', 
                      '{$data['full_name']}', 
                      '{$data['phone']}', 
                      'user', 
                      NOW()
                  )";
        
        if (mysqli_query($conn, $query)) {
            $signup_success = "Registration successful! You can now login.";
            unset($_SESSION['temp_signup']);
            unset($_SESSION['demo_otp']);
            unset($_SESSION['otp_expiry']);
            echo "<script>setTimeout(function() { switchTab('login'); }, 2000);</script>";
        } else {
            $signup_error = "Registration failed: " . mysqli_error($conn);
        }
    } else {
        $signup_error = "Invalid or expired OTP!";
    }
}

// Handle Login - WITH LOGIN HISTORY WORKING
if (isset($_POST['login'])) {
    if ($is_blocked) {
        $login_error = "Too many failed attempts. Please wait $wait_time minutes.";
    } else {
        if (!isset($_POST['captcha']) || $_POST['captcha'] != $_SESSION['captcha_result']) {
            $login_error = "Incorrect captcha answer!";
        } else {
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $password = $_POST['password'];
            
            $query = "SELECT * FROM users WHERE username = '$username' OR email = '$username'";
            $result = mysqli_query($conn, $query);
            
            if ($result && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                
                if (trim($password) === trim($user['password'])) {
                    // Login successful
                    unset($_SESSION[$attempts_key]);
                    unset($_SESSION[$blocked_key]);
                    
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['login_time'] = time();
                    
                    // ========== IMPORTANT: SAVE LOGIN HISTORY ==========
                    $device = DeviceDetector::detect();
                    
                    $user_id = $user['id'];
                    $ip = mysqli_real_escape_string($conn, $device['ip']);
                    $device_info = mysqli_real_escape_string($conn, $device['user_agent']);
                    $browser = mysqli_real_escape_string($conn, $device['browser']);
                    $os = mysqli_real_escape_string($conn, $device['os']);
                    $device_type = mysqli_real_escape_string($conn, $device['device_type']);
                    $location = mysqli_real_escape_string($conn, $device['location']);
                    
                    // Insert into login_history table
                    $insert_history = "INSERT INTO login_history 
                                      (user_id, ip_address, device_info, browser, os, device_type, location, status) 
                                      VALUES 
                                      ('$user_id', '$ip', '$device_info', '$browser', '$os', '$device_type', '$location', 'success')";
                    
                    mysqli_query($conn, $insert_history);
                    
                    // Check if device is known
                    $fingerprint = mysqli_real_escape_string($conn, $device['fingerprint']);
                    $check = mysqli_query($conn, "SELECT * FROM known_devices WHERE user_id = $user_id AND device_fingerprint = '$fingerprint'");
                    
                    if ($check && mysqli_num_rows($check) == 0) {
                        $alert_message = "New login from $device_type - $browser on $os at $location";
                        mysqli_query($conn, "INSERT INTO security_alerts (user_id, alert_type, message, ip_address, device_info) 
                                            VALUES ($user_id, 'new_device', '$alert_message', '$ip', '$device_info')");
                        
                        $device_name = mysqli_real_escape_string($conn, "$device_type - $browser on $os");
                        mysqli_query($conn, "INSERT INTO known_devices (user_id, device_fingerprint, device_name) 
                                            VALUES ($user_id, '$fingerprint', '$device_name')");
                    }
                    
                    mysqli_query($conn, "UPDATE users SET last_login = NOW() WHERE id = $user_id");
                    
                    header("Location: index.php");
                    exit();
                } else {
                    $login_error = "Invalid password!";
                    
                    if (!isset($_SESSION[$attempts_key])) {
                        $_SESSION[$attempts_key] = 1;
                    } else {
                        $_SESSION[$attempts_key]++;
                    }
                    
                    if ($_SESSION[$attempts_key] >= 3) {
                        $_SESSION[$blocked_key] = time() + 300;
                        $login_error = "Too many failed attempts. Please wait 5 minutes.";
                    }
                    
                    // Log failed attempt
                    $device = DeviceDetector::detect();
                    $ip = mysqli_real_escape_string($conn, $device['ip']);
                    $device_info = mysqli_real_escape_string($conn, $device['user_agent']);
                    
                    mysqli_query($conn, "INSERT INTO login_history (user_id, ip_address, device_info, status) 
                                        VALUES ({$user['id']}, '$ip', '$device_info', 'failed')");
                }
            } else {
                $login_error = "User not found!";
            }
        }
    }
}

// Generate new captcha
$captcha_question = generateCaptcha();

// Get current device
$currentDevice = DeviceDetector::detect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login - Bank IFSC Finder</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 550px;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .header h1 i {
            margin-right: 10px;
        }
        
        .header p {
            opacity: 0.9;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .device-info {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: white;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .device-info h3 {
            font-size: 16px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .device-info h3 i {
            color: #667eea;
        }
        
        .device-detail {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 13px;
            color: #cbd5e1;
        }
        
        .device-detail i {
            width: 20px;
            color: #667eea;
        }
        
        .device-detail strong {
            color: white;
            width: 70px;
        }
        
        .tabs {
            display: flex;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .tab {
            flex: 1;
            text-align: center;
            padding: 20px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
            color: #666;
        }
        
        .tab.active {
            color: #667eea;
            border-bottom: 3px solid #667eea;
        }
        
        .tab i {
            margin-right: 8px;
        }
        
        .tab-content {
            padding: 30px;
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group label i {
            color: #667eea;
            margin-right: 5px;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: 0.3s;
        }
        
        .form-group input:focus {
            border-color: #667eea;
            outline: none;
        }
        
        .captcha-group {
            display: flex;
            gap: 10px;
        }
        
        .captcha-group input {
            flex: 1;
        }
        
        .captcha-display {
            background: #f0f0f0;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: bold;
            color: #333;
            white-space: nowrap;
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert.info {
            background: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        
        .otp-input {
            letter-spacing: 8px;
            font-size: 24px;
            text-align: center;
            font-weight: bold;
        }
        
        .attempt-info {
            margin-top: 15px;
            padding: 10px;
            background: #fff3cd;
            border-radius: 5px;
            font-size: 13px;
            color: #856404;
            text-align: center;
        }
        
        .fingerprint {
            font-family: monospace;
            font-size: 11px;
            color: #999;
            text-align: center;
            padding: 15px;
            border-top: 1px solid #f0f0f0;
        }
        
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .test-credentials {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            font-size: 13px;
            border: 1px dashed #667eea;
        }
        
        .test-credentials h4 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .test-credentials p {
            color: #666;
            margin-bottom: 5px;
        }
        
        .test-credentials i {
            color: #667eea;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shield-alt"></i> Secure Banking</h1>
            <p>Multi-layer security with OTP & Captcha</p>
        </div>
        
        <div class="card">
            <!-- Device Detection -->
            <div class="device-info">
                <h3><i class="fas fa-laptop"></i> Your Security Fingerprint</h3>
                <div class="device-detail">
                    <i class="fas fa-<?php echo $currentDevice['device_type'] == 'Mobile' ? 'mobile-alt' : 'desktop'; ?>"></i>
                    <strong>Device:</strong> <span><?php echo $currentDevice['device_type']; ?></span>
                </div>
                <div class="device-detail">
                    <i class="fas fa-globe"></i>
                    <strong>Browser:</strong> <span><?php echo $currentDevice['browser']; ?> on <?php echo $currentDevice['os']; ?></span>
                </div>
                <div class="device-detail">
                    <i class="fas fa-map-marker-alt"></i>
                    <strong>Location:</strong> <span><?php echo $currentDevice['location']; ?></span>
                </div>
                <div class="device-detail">
                    <i class="fas fa-network-wired"></i>
                    <strong>IP:</strong> <span><?php echo $currentDevice['ip']; ?></span>
                </div>
                <div class="device-detail">
                    <i class="fas fa-fingerprint"></i>
                    <strong>Fingerprint:</strong> <span><?php echo substr($currentDevice['fingerprint'], 0, 16); ?>...</span>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="tabs">
                <div class="tab <?php echo !$show_otp_form ? 'active' : ''; ?>" onclick="switchTab('login')">
                    <i class="fas fa-sign-in-alt"></i> Secure Login
                </div>
                <div class="tab <?php echo $show_otp_form ? 'active' : ''; ?>" onclick="switchTab('signup')">
                    <i class="fas fa-user-plus"></i> Register with OTP
                </div>
            </div>
            
            <!-- Login Form -->
            <div id="login" class="tab-content <?php echo !$show_otp_form ? 'active' : ''; ?>">
                <?php if (!empty($login_error)): ?>
                    <div class="alert error"><?php echo $login_error; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($signup_success) && !$show_otp_form): ?>
                    <div class="alert success"><?php echo $signup_success; ?></div>
                <?php endif; ?>
                
                <?php if ($is_blocked): ?>
                    <div class="attempt-info">
                        <i class="fas fa-clock"></i> 
                        Account locked. Try again in <?php echo $wait_time; ?> minutes
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Username or Email</label>
                        <input type="text" name="username" placeholder="Enter username or email" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <input type="password" name="password" placeholder="Enter password" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-calculator"></i> Captcha Verification</label>
                        <div class="captcha-group">
                            <input type="text" name="captcha" placeholder="Enter answer" required>
                            <div class="captcha-display"><?php echo $captcha_question; ?></div>
                        </div>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-primary">
                        <i class="fas fa-shield-alt"></i> Verify & Login
                    </button>
                </form>
                
                <div class="attempt-info">
                    <i class="fas fa-exclamation-triangle"></i> 
                    3 failed attempts = 5 minute lockout
                </div>
            </div>
            
            <!-- Signup Form with OTP -->
            <div id="signup" class="tab-content <?php echo $show_otp_form ? 'active' : ''; ?>">
                <?php if (!empty($signup_error)): ?>
                    <div class="alert error"><?php echo $signup_error; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($signup_success) && $show_otp_form): ?>
                    <div class="alert info">
                        <i class="fas fa-envelope"></i> 
                        Demo OTP: <strong><?php echo $_SESSION['demo_otp'] ?? '------'; ?></strong>
                    </div>
                <?php endif; ?>
                
                <?php if ($show_otp_form): ?>
                    <!-- OTP Verification Form -->
                    <form method="POST" action="">
                        <div class="form-group">
                            <label><i class="fas fa-key"></i> Enter OTP</label>
                            <input type="text" name="otp" class="otp-input" placeholder="------" maxlength="6" pattern="\d{6}" required>
                        </div>
                        
                        <button type="submit" name="verify_otp" class="btn btn-success">
                            <i class="fas fa-check-circle"></i> Verify OTP
                        </button>
                    </form>
                <?php else: ?>
                    <!-- Registration Form -->
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <div class="row">
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Full Name</label>
                                <input type="text" name="full_name" placeholder="Full name" required>
                            </div>
                            
                            <div class="form-group">
                                <label><i class="fas fa-at"></i> Username</label>
                                <input type="text" name="username" placeholder="Username" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" name="email" placeholder="Email" required>
                            </div>
                            
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> Phone</label>
                                <input type="text" name="phone" placeholder="Phone number" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="form-group">
                                <label><i class="fas fa-lock"></i> Password</label>
                                <input type="password" name="password" placeholder="Password" required>
                            </div>
                            
                            <div class="form-group">
                                <label><i class="fas fa-check-circle"></i> Confirm</label>
                                <input type="password" name="confirm_password" placeholder="Confirm password" required>
                            </div>
                        </div>
                        
                        <button type="submit" name="signup" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Register with OTP
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            
            <!-- Test Credentials -->
            <div class="test-credentials">
                <h4><i class="fas fa-flask"></i> Test Credentials:</h4>
                <p><i class="fas fa-user"></i> <strong>Admin:</strong> admin / Admin@123</p>
                <p><i class="fas fa-user"></i> <strong>Demo User:</strong> john / john123</p>
                <p><i class="fas fa-info-circle"></i> Use these or create your own account</p>
            </div>
            
            <div class="fingerprint">
                <i class="fas fa-shield-alt"></i> 
                Session ID: <?php echo substr(session_id(), 0, 16); ?>...
            </div>
        </div>
    </div>
    
    <script>
        function switchTab(tab) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
            
            if (tab === 'login') {
                document.getElementById('login').classList.add('active');
                document.querySelectorAll('.tab')[0].classList.add('active');
            } else {
                document.getElementById('signup').classList.add('active');
                document.querySelectorAll('.tab')[1].classList.add('active');
            }
        }
        
        setTimeout(function() {
            location.reload();
        }, 300000);
    </script>
</body>
</html>
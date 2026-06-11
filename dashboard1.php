<?php
// dashboard1.php - User dashboard with real security data
session_start();
require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login1.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Get login history
$query = "SELECT * FROM login_history WHERE user_id = $user_id ORDER BY login_time DESC LIMIT 10";
$history = mysqli_query($conn, $query);

// Get security alerts
$query = "SELECT * FROM security_alerts WHERE user_id = $user_id ORDER BY created_at DESC";
$alerts = mysqli_query($conn, $query);
$alert_count = mysqli_num_rows($alerts);

// Get known devices
$query = "SELECT * FROM known_devices WHERE user_id = $user_id ORDER BY last_seen DESC";
$devices = mysqli_query($conn, $query);

// Mark alerts as read
mysqli_query($conn, "UPDATE security_alerts SET is_read = TRUE WHERE user_id = $user_id");

// Device Detector class for current session
class DeviceDetector {
    public static function getCurrentDevice() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        // Detect device type
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent)) {
            return 'Mobile';
        } elseif (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $userAgent)) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }
}

$currentDevice = DeviceDetector::getCurrentDevice();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bank Security</title>
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
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            border-radius: 15px;
            padding: 20px 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header h1 {
            color: #333;
            font-size: 24px;
        }
        
        .header h1 i {
            color: #667eea;
            margin-right: 10px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .badge.admin {
            background: #dc3545;
            color: white;
        }
        
        .badge.user {
            background: #28a745;
            color: white;
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .logout-btn:hover {
            background: #c82333;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .stat-card h3 i {
            color: #667eea;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }
        
        .stat-label {
            color: #999;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .alert-badge {
            background: #dc3545;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            margin-left: 10px;
        }
        
        .section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .section h2 i {
            color: #667eea;
            margin-right: 10px;
        }
        
        .section h2 span {
            font-size: 14px;
            font-weight: normal;
            color: #666;
        }
        
        .history-item, .alert-item, .device-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: 0.3s;
            gap: 15px;
        }
        
        .history-item:hover, .alert-item:hover, .device-item:hover {
            background: #f8f9fa;
        }
        
        .history-icon, .alert-icon, .device-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        
        .history-icon.success { background: #d4edda; color: #155724; }
        .history-icon.failed { background: #f8d7da; color: #721c24; }
        .alert-icon { background: #fff3cd; color: #856404; }
        .device-icon { background: #e0e7ff; color: #667eea; }
        
        .history-details, .alert-details, .device-details {
            flex: 1;
        }
        
        .history-title, .alert-title, .device-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .history-meta, .alert-meta, .device-meta {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: #999;
            flex-wrap: wrap;
        }
        
        .location-badge {
            background: #667eea;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 11px;
        }
        
        .trusted-badge {
            background: #28a745;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 11px;
        }
        
        .untrusted-badge {
            background: #ffc107;
            color: #333;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 11px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            font-size: 14px;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .nav-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #667eea;
            padding: 10px 20px;
            border-radius: 8px;
            transition: 0.3s;
            background: #f8f9fa;
        }
        
        .nav-links a:hover {
            background: #667eea;
            color: white;
        }
        
        .nav-links a i {
            margin-right: 5px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }
        
        .device-info-current {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .device-info-current i {
            font-size: 24px;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }
            
            .user-info {
                justify-content: center;
            }
            
            .history-meta, .alert-meta, .device-meta {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-shield-alt"></i> 
                Security Dashboard
                <span style="font-size: 14px; color: #666; margin-left: 10px;">
                    <i class="fas fa-<?php echo $currentDevice == 'Mobile' ? 'mobile-alt' : 'desktop'; ?>"></i>
                    <?php echo $currentDevice; ?> Session
                </span>
            </h1>
            
            <div class="user-info">
                <div>
                    <i class="fas fa-user-circle" style="font-size: 24px; color: #667eea;"></i>
                    <span style="margin-left: 8px;"><strong><?php echo $user['full_name'] ?: $user['username']; ?></strong></span>
                </div>
                
                <span class="badge <?php echo $user['role']; ?>">
                    <i class="fas fa-<?php echo $user['role'] == 'admin' ? 'crown' : 'user'; ?>"></i>
                    <?php echo ucfirst($user['role']); ?>
                </span>
                
                <?php if ($user['two_factor_enabled']): ?>
                <span class="badge" style="background: #17a2b8; color: white;">
                    <i class="fas fa-lock"></i> 2FA Enabled
                </span>
                <?php endif; ?>
                
                <?php if ($user['biometric_enabled']): ?>
                <span class="badge" style="background: #28a745; color: white;">
                    <i class="fas fa-fingerprint"></i> Biometric
                </span>
                <?php endif; ?>
                
                <button class="logout-btn" onclick="window.location.href='logout.php'">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="stats-grid">
            <?php
            $total_logins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM login_history WHERE user_id = $user_id"))['count'];
            $total_devices = mysqli_num_rows($devices);
            $total_alerts = $alert_count;
            $last_login = mysqli_fetch_assoc(mysqli_query($conn, "SELECT login_time FROM login_history WHERE user_id = $user_id AND status='success' ORDER BY login_time DESC LIMIT 1"));
            ?>
            
            <div class="stat-card">
                <h3><i class="fas fa-history"></i> Total Logins</h3>
                <div class="stat-number"><?php echo $total_logins; ?></div>
                <div class="stat-label">All time login count</div>
            </div>
            
            <div class="stat-card">
                <h3><i class="fas fa-laptop"></i> Known Devices</h3>
                <div class="stat-number"><?php echo $total_devices; ?></div>
                <div class="stat-label">Trusted & untrusted devices</div>
            </div>
            
            <div class="stat-card">
                <h3><i class="fas fa-exclamation-triangle"></i> Security Alerts</h3>
                <div class="stat-number"><?php echo $total_alerts; ?></div>
                <div class="stat-label"><?php echo $total_alerts > 0 ? 'New alerts detected' : 'No new alerts'; ?></div>
            </div>
            
            <div class="stat-card">
                <h3><i class="fas fa-clock"></i> Last Login</h3>
                <div class="stat-number">
                    <?php echo $last_login ? date('d M', strtotime($last_login['login_time'])) : 'N/A'; ?>
                </div>
                <div class="stat-label">
                    <?php echo $last_login ? date('h:i A', strtotime($last_login['login_time'])) : 'Never'; ?>
                </div>
            </div>
        </div>
        
        <!-- Current Device Info -->
        <div class="device-info-current">
            <i class="fas fa-<?php echo $currentDevice == 'Mobile' ? 'mobile-alt' : ($currentDevice == 'Tablet' ? 'tablet-alt' : 'laptop'); ?>"></i>
            <div>
                <strong>Current Session:</strong> You are using a <?php echo $currentDevice; ?> device
                <span style="margin-left: 10px; font-size: 12px; opacity: 0.8;">
                    <i class="fas fa-clock"></i> Started: <?php echo date('d M Y h:i A', $_SESSION['last_activity'] ?? time()); ?>
                </span>
            </div>
        </div>
        
        <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 25px;">
            <!-- Login History -->
            <div class="section">
                <h2>
                    <span><i class="fas fa-history"></i> Recent Login Activity</span>
                    <span>Last 10 logins</span>
                </h2>
                
                <?php if (mysqli_num_rows($history) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($history)): ?>
                    <div class="history-item">
                        <div class="history-icon <?php echo $row['status']; ?>">
                            <i class="fas fa-<?php echo $row['status'] == 'success' ? 'check' : 'times'; ?>"></i>
                        </div>
                        <div class="history-details">
                            <div class="history-title">
                                <?php echo ucfirst($row['status']); ?> Login
                                <span class="location-badge">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo $row['location'] ?: 'Unknown'; ?>
                                </span>
                            </div>
                            <div class="history-meta">
                                <span><i class="fas fa-<?php echo $row['device_type'] == 'Mobile' ? 'mobile-alt' : ($row['device_type'] == 'Tablet' ? 'tablet-alt' : 'desktop'); ?>"></i> <?php echo $row['device_type']; ?></span>
                                <span><i class="fas fa-globe"></i> <?php echo $row['browser']; ?></span>
                                <span><i class="fas fa-microchip"></i> <?php echo $row['os']; ?></span>
                                <span><i class="fas fa-network-wired"></i> <?php echo $row['ip_address']; ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo date('d M Y h:i A', strtotime($row['login_time'])); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-history"></i>
                        <p>No login history yet</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Security Alerts -->
            <div class="section">
                <h2>
                    <span><i class="fas fa-exclamation-triangle"></i> Security Alerts</span>
                    <?php if ($alert_count > 0): ?>
                    <span class="alert-badge"><?php echo $alert_count; ?> New</span>
                    <?php endif; ?>
                </h2>
                
                <?php if (mysqli_num_rows($alerts) > 0): ?>
                    <?php while($alert = mysqli_fetch_assoc($alerts)): ?>
                    <div class="alert-item">
                        <div class="alert-icon">
                            <i class="fas fa-<?php echo $alert['alert_type'] == 'new_device' ? 'laptop' : 'shield'; ?>"></i>
                        </div>
                        <div class="alert-details">
                            <div class="alert-title">
                                <?php echo $alert['message']; ?>
                                <?php if (!$alert['is_read']): ?>
                                <span class="alert-badge" style="margin-left: 10px;">New</span>
                                <?php endif; ?>
                            </div>
                            <div class="alert-meta">
                                <span><i class="fas fa-clock"></i> <?php echo date('d M Y h:i A', strtotime($alert['created_at'])); ?></span>
                                <span><i class="fas fa-network-wired"></i> <?php echo $alert['ip_address']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-shield-alt"></i>
                        <p>No security alerts</p>
                        <small>Your account is secure</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Known Devices -->
        <div class="section">
            <h2>
                <span><i class="fas fa-laptop"></i> Known Devices</span>
                <span><?php echo mysqli_num_rows($devices); ?> device(s)</span>
            </h2>
            
            <?php if (mysqli_num_rows($devices) > 0): ?>
                <?php while($device = mysqli_fetch_assoc($devices)): ?>
                <div class="device-item">
                    <div class="device-icon">
                        <i class="fas fa-<?php echo strpos($device['device_name'], 'Mobile') !== false ? 'mobile-alt' : (strpos($device['device_name'], 'Tablet') !== false ? 'tablet-alt' : 'desktop'); ?>"></i>
                    </div>
                    <div class="device-details">
                        <div class="device-title">
                            <?php echo $device['device_name']; ?>
                            <?php if ($device['is_trusted']): ?>
                                <span class="trusted-badge"><i class="fas fa-check-circle"></i> Trusted</span>
                            <?php else: ?>
                                <span class="untrusted-badge"><i class="fas fa-clock"></i> New Device</span>
                            <?php endif; ?>
                        </div>
                        <div class="device-meta">
                            <span><i class="fas fa-fingerprint"></i> ID: <?php echo substr($device['device_fingerprint'], 0, 16); ?>...</span>
                            <span><i class="fas fa-clock"></i> Last seen: <?php echo date('d M Y h:i A', strtotime($device['last_seen'])); ?></span>
                        </div>
                    </div>
                    <div>
                        <?php if (!$device['is_trusted']): ?>
                        <button class="btn btn-success btn-sm" onclick="trustDevice(<?php echo $device['id']; ?>)">
                            <i class="fas fa-check"></i> Trust
                        </button>
                        <?php endif; ?>
                        <button class="btn btn-danger btn-sm" onclick="removeDevice(<?php echo $device['id']; ?>)">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-laptop"></i>
                    <p>No devices registered yet</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Navigation Links -->
        <div class="nav-links">
            <a href="bankdetails.php?ifsc=SBIN0001001">
                <i class="fas fa-university"></i> Bank Details
            </a>
            <a href="security.php">
                <i class="fas fa-shield-alt"></i> Security Panel
            </a>
            <a href="profile.php">
                <i class="fas fa-user-cog"></i> Profile Settings
            </a>
            <?php if ($user['role'] == 'admin'): ?>
            <a href="admin.php">
                <i class="fas fa-crown"></i> Admin Panel
            </a>
            <?php endif; ?>
            <a href="#" onclick="enable2FA()">
                <i class="fas fa-lock"></i> Enable 2FA
            </a>
            <a href="#" onclick="enableBiometric()">
                <i class="fas fa-fingerprint"></i> Enable Biometric
            </a>
        </div>
    </div>
    
    <script>
        // Device management functions
        function trustDevice(deviceId) {
            if (confirm('Trust this device? You won\'t be alerted for future logins.')) {
                // In production, make AJAX call
                alert('Device trusted successfully!');
                location.reload();
            }
        }
        
        function removeDevice(deviceId) {
            if (confirm('Remove this device from known devices?')) {
                // In production, make AJAX call
                alert('Device removed successfully!');
                location.reload();
            }
        }
        
        function enable2FA() {
            alert('2FA setup email sent to <?php echo $user['email']; ?>');
        }
        
        function enableBiometric() {
            if ('<?php echo $currentDevice; ?>' === 'Mobile') {
                alert('Biometric setup initiated. Please follow device instructions.');
            } else {
                alert('Windows Hello setup initiated.');
            }
        }
        
        // Auto refresh alerts every 30 seconds
        setInterval(function() {
            // In production, check for new alerts via AJAX
            console.log('Checking for new alerts...');
        }, 30000);
        
        // Session warning (5 minutes before timeout)
        setTimeout(function() {
            alert('⚠️ Your session will expire in 5 minutes. Please save your work.');
        }, 25 * 60 * 1000); // 25 minutes
    </script>
</body>
</html>
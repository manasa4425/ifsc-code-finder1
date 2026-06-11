<?php
// security.php - Admin Security Control Panel
session_start();
require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login1.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'] ?? '';
$role = $_SESSION['role'];

// Get user details
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_query);

// Check if user is admin (only admin can access full features)
$is_admin = ($role === 'admin');

// Get unread security alerts count
$alert_count = 0;
if (isset($_SESSION['user_id'])) {
    $alerts_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM security_alerts WHERE user_id = $user_id AND is_read = FALSE");
    if ($alerts_query) {
        $alert_count = mysqli_fetch_assoc($alerts_query)['count'];
    }
}

// Handle admin actions (only for admin)
$action_message = '';

if ($is_admin && isset($_POST['action'])) {
    if ($_POST['action'] === 'block_user' && isset($_POST['user_id'])) {
        $block_user_id = (int)$_POST['user_id'];
        $block_reason = mysqli_real_escape_string($conn, $_POST['reason']);
        
        // Block user (set role to 'blocked' or delete sessions)
        mysqli_query($conn, "UPDATE users SET role = 'blocked' WHERE id = $block_user_id");
        
        // Clear all their sessions (in production, you'd want to invalidate all sessions)
        mysqli_query($conn, "DELETE FROM login_history WHERE user_id = $block_user_id");
        
        $action_message = "<div class='alert success'>User blocked successfully!</div>";
    }
    
    if ($_POST['action'] === 'unblock_user' && isset($_POST['user_id'])) {
        $unblock_user_id = (int)$_POST['user_id'];
        mysqli_query($conn, "UPDATE users SET role = 'user' WHERE id = $unblock_user_id");
        $action_message = "<div class='alert success'>User unblocked successfully!</div>";
    }
}

// Get all users (for admin panel)
$all_users = [];
if ($is_admin) {
    $users_query = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
    $all_users = mysqli_fetch_all($users_query, MYSQLI_ASSOC);
}

// Get login history for this user (or all users if admin)
if ($is_admin) {
    $history_query = mysqli_query($conn, "SELECT l.*, u.username, u.full_name 
                                          FROM login_history l 
                                          JOIN users u ON l.user_id = u.id 
                                          ORDER BY l.login_time DESC 
                                          LIMIT 50");
} else {
    $history_query = mysqli_query($conn, "SELECT * FROM login_history 
                                          WHERE user_id = $user_id 
                                          ORDER BY login_time DESC 
                                          LIMIT 20");
}
$login_history = mysqli_fetch_all($history_query, MYSQLI_ASSOC);

// Get security alerts
if ($is_admin) {
    $alerts_query = mysqli_query($conn, "SELECT a.*, u.username, u.full_name 
                                         FROM security_alerts a 
                                         JOIN users u ON a.user_id = u.id 
                                         ORDER BY a.created_at DESC 
                                         LIMIT 50");
} else {
    $alerts_query = mysqli_query($conn, "SELECT * FROM security_alerts 
                                         WHERE user_id = $user_id 
                                         ORDER BY created_at DESC");
}
$alerts = mysqli_fetch_all($alerts_query, MYSQLI_ASSOC);

// Get known devices
if ($is_admin) {
    $devices_query = mysqli_query($conn, "SELECT d.*, u.username, u.full_name 
                                          FROM known_devices d 
                                          JOIN users u ON d.user_id = u.id 
                                          ORDER BY d.last_seen DESC");
} else {
    $devices_query = mysqli_query($conn, "SELECT * FROM known_devices 
                                          WHERE user_id = $user_id 
                                          ORDER BY last_seen DESC");
}
$devices = mysqli_fetch_all($devices_query, MYSQLI_ASSOC);

// Get login attempt stats
$failed_attempts_today = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as count FROM login_history 
     WHERE status = 'failed' AND DATE(login_time) = CURDATE()"
))['count'];

$blocked_ips = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(DISTINCT ip_address) as count FROM login_history 
     WHERE status = 'failed' AND login_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)"
))['count'];

// Get device stats
$mobile_logins = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as count FROM login_history WHERE device_type = 'Mobile'"
))['count'];

$desktop_logins = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as count FROM login_history WHERE device_type = 'Desktop'"
))['count'];

$tablet_logins = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as count FROM login_history WHERE device_type = 'Tablet'"
))['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Control Panel - Bank IFSC Finder</title>
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
        
        /* Header - Same as other pages */
        .header {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background: white;
            border-radius: 12px;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            position: relative;
            z-index: 100;
        }
        
        .three-dots {
            font-size: 24px;
            cursor: pointer;
            margin-right: 20px;
            color: #2563EB;
            transition: all 0.3s;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .three-dots:hover {
            background: #EFF6FF;
            transform: scale(1.1);
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1A2B4C;
            flex-shrink: 0;
        }
        
        .logo span {
            color: #2563EB;
            font-size: 28px;
        }
        
        .logo i {
            color: #2563EB;
            margin-right: 8px;
        }
        
        /* User Info Section */
        .user-info {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-details {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #F8FAFC;
            padding: 6px 15px;
            border-radius: 30px;
            border: 1px solid #E2E8F0;
            white-space: nowrap;
        }
        
        .user-details i {
            color: #2563EB;
            font-size: 16px;
        }
        
        .user-details span {
            color: #1E293B;
            font-size: 14px;
            font-weight: 500;
        }
        
        .security-link {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #EFF6FF;
            color: #2563EB;
            text-decoration: none;
            padding: 6px 15px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid #2563EB;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .security-link:hover {
            background: #2563EB;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.2);
        }
        
        .alert-badge {
            background: #DC2626;
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 5px;
            font-weight: 600;
        }
        
        .logout-btn {
            background: #DC2626;
            color: white;
            text-decoration: none;
            padding: 6px 15px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }
        
        .logout-btn:hover {
            background: #B91C1C;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 38, 38, 0.2);
        }
        
        /* Dropdown Menu */
        .dropdown {
            position: absolute;
            top: 65px;
            left: 20px;
            width: 280px;
            background: #0f4c75;
            border-radius: 12px;
            display: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
            overflow: hidden;
            z-index: 1000;
            border: 1px solid rgba(255,255,255,0.1);
            animation: slideDown 0.3s ease;
        }
        
        .dropdown-user {
            background: #1e3c5c;
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .dropdown-user .user-name {
            color: white;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .dropdown-user .user-email {
            color: #a0c0e0;
            font-size: 12px;
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
            padding: 15px 18px;
            color: #fff;
            text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s;
            font-size: 15px;
        }
        
        .dropdown a:hover {
            background: #3282b8;
            padding-left: 25px;
        }
        
        .dropdown a i {
            width: 20px;
            color: #00FFD1;
        }
        
        .dropdown a:last-child {
            border-bottom: none;
        }
        
        .dropdown.show {
            display: block;
        }
        
        /* Rest of your existing styles */
        .role-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .role-badge.admin {
            background: #dc3545;
            color: white;
        }
        
        .role-badge.user {
            background: #28a745;
            color: white;
        }
        
        .role-badge.blocked {
            background: #6c757d;
            color: white;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card i {
            font-size: 30px;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .stat-card .number {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }
        
        .stat-card .label {
            color: #666;
            font-size: 14px;
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
        
        .admin-only {
            background: #dc3545;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .device-badge {
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .device-badge.mobile { background: #17a2b8; color: white; }
        .device-badge.desktop { background: #28a745; color: white; }
        .device-badge.tablet { background: #ffc107; color: #333; }
        
        .status-badge {
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
        }
        
        .status-badge.success { background: #d4edda; color: #155724; }
        .status-badge.failed { background: #f8d7da; color: #721c24; }
        
        .block-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .block-btn:hover {
            background: #c82333;
        }
        
        .unblock-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
        }
        
        .alert.warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
        }
        
        .modal-content h3 {
            margin-bottom: 20px;
        }
        
        .modal-content input, .modal-content textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
        }
        
        .modal-buttons {
            display: flex;
            gap: 10px;
        }
        
        /* Live Clock */
        .live-clock {
            position: fixed;
            bottom: 25px;
            right: 35px;
            background: white;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 600;
            color: #1E293B;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            font-size: 14px;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(5px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .live-clock i {
            color: #667eea;
            margin-right: 5px;
        }
        
        /* Footer Note */
        .footer-note {
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
        }
        
        .footer-note i {
            color: #10B981;
            margin-right: 5px;
        }
        
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .user-info {
                margin-left: 0;
                width: 100%;
                justify-content: center;
            }
            
            .dropdown {
                left: 10px;
                right: 10px;
                width: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Header with 3-dot menu -->
    <div class="header">
        <div class="three-dots" onclick="toggleMenu()">
            <i class="fa-solid fa-ellipsis-vertical"></i>
        </div>

        <div class="logo">
            <i class="fa-solid fa-building-columns"></i> BANK <span>IFSC Finder</span>
        </div>

        <!-- User Info Section -->
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-info">
            <div class="user-details">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($full_name ?: $username); ?></span>
                <span class="role-badge <?php echo $user['role']; ?>" style="margin-left:8px;">
                    <i class="fas fa-<?php echo $is_admin ? 'crown' : 'user'; ?>"></i>
                    <?php echo ucfirst($user['role']); ?>
                </span>
            </div>
            
            <a href="security.php" class="security-link">
                <i class="fas fa-shield-alt"></i> <span>Security</span>
                <?php if ($alert_count > 0): ?>
                    <span class="alert-badge"><?php echo $alert_count; ?></span>
                <?php endif; ?>
            </a>
            
            <a href="logout2.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
        <?php endif; ?>

        <!-- Dropdown Menu -->
        <div class="dropdown" id="menu">
            <!-- Show user info in dropdown if logged in -->
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="dropdown-user">
                <div class="user-name"><?php echo htmlspecialchars($full_name ?: $username); ?></div>
                <div class="user-email"><?php echo ucfirst($user['role']); ?> • Logged in</div>
            </div>
            <?php endif; ?>
            
            <a href="index.php"><i class="fa fa-home"></i> Home</a>
            <a href="security.php">
                <i class="fa fa-shield-halved"></i> Security Panel
                <?php if ($alert_count > 0): ?>
                    <span style="background:#DC2626;color:white;padding:2px 8px;border-radius:10px;margin-left:5px;">
                        <?php echo $alert_count; ?>
                    </span>
                <?php endif; ?>
            </a>
            <a href="about.php"><i class="fa fa-info-circle"></i> About</a>
            <a href="adminlogin.php"><i class="fa fa-user-shield"></i> Admin Login</a>
            <a href="banklogin.php"><i class="fa fa-university"></i> Bank Login</a>
            <a href="customerlogin.php"><i class="fa fa-users"></i> Customer Login</a>
            <a href="bankdetails.php"><i class="fa fa-building-columns"></i> Bank Details</a>
            <a href="contact.php"><i class="fa fa-phone"></i> Contact</a>
            <a href="settings.php"><i class="fa fa-gear"></i> Settings</a>
            
            <!-- Show logout in dropdown if user is logged in -->
            <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout2.php" style="background:#DC2626;"><i class="fa fa-sign-out-alt"></i> Logout</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container">
        <?php echo $action_message; ?>
        
        <?php if (!$is_admin): ?>
            <!-- Regular User View -->
            <div class="alert warning" style="margin-bottom:20px;">
                <i class="fas fa-info-circle"></i> 
                You are viewing your personal security data. Admin features are hidden.
            </div>
        <?php endif; ?>
        
        <!-- Stats Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-history"></i>
                <div class="number"><?php echo count($login_history); ?></div>
                <div class="label">Recent Logins</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="number"><?php echo count($alerts); ?></div>
                <div class="label">Security Alerts</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-laptop"></i>
                <div class="number"><?php echo count($devices); ?></div>
                <div class="label">Known Devices</div>
            </div>
            
            <?php if ($is_admin): ?>
            <div class="stat-card">
                <i class="fas fa-ban"></i>
                <div class="number"><?php echo $failed_attempts_today; ?></div>
                <div class="label">Failed Attempts Today</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-network-wired"></i>
                <div class="number"><?php echo $blocked_ips; ?></div>
                <div class="label">Active Threats</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-mobile-alt"></i>
                <div class="number"><?php echo $mobile_logins; ?></div>
                <div class="label">Mobile Logins</div>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-desktop"></i>
                <div class="number"><?php echo $desktop_logins; ?></div>
                <div class="label">Desktop Logins</div>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($is_admin): ?>
        <!-- Admin Only: Users Management -->
        <div class="section">
            <h2>
                <span><i class="fas fa-users"></i> User Management</span>
                <span class="admin-only">Admin Only</span>
            </h2>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>2FA</th>
                        <th>Joined</th>
                        <th>Last Login</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_users as $u): ?>
                    <tr>
                        <td>#<?php echo $u['id']; ?></td>
                        <td><?php echo $u['username']; ?></td>
                        <td><?php echo $u['full_name']; ?></td>
                        <td><?php echo $u['email']; ?></td>
                        <td>
                            <span class="role-badge <?php echo $u['role']; ?>">
                                <?php echo ucfirst($u['role']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['two_factor_enabled']): ?>
                                <i class="fas fa-check-circle" style="color:#28a745;"></i> Enabled
                            <?php else: ?>
                                <i class="fas fa-times-circle" style="color:#dc3545;"></i> Disabled
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                        <td><?php echo $u['last_login'] ? date('d M H:i', strtotime($u['last_login'])) : 'Never'; ?></td>
                        <td>
                            <?php if ($u['role'] === 'blocked'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="unblock_user">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" class="unblock-btn" onclick="return confirm('Unblock this user?')">
                                        <i class="fas fa-unlock"></i> Unblock
                                    </button>
                                </form>
                            <?php elseif ($u['role'] !== 'admin'): ?>
                                <button class="block-btn" onclick="showBlockModal(<?php echo $u['id']; ?>, '<?php echo $u['username']; ?>')">
                                    <i class="fas fa-ban"></i> Block
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <!-- Login History -->
        <div class="section">
            <h2>
                <span><i class="fas fa-history"></i> Login History</span>
                <?php if ($is_admin): ?>
                <span class="admin-only">All Users</span>
                <?php endif; ?>
            </h2>
            
            <table>
                <thead>
                    <tr>
                        <?php if ($is_admin): ?>
                        <th>User</th>
                        <?php endif; ?>
                        <th>Time</th>
                        <th>IP Address</th>
                        <th>Device</th>
                        <th>Browser/OS</th>
                        <th>Location</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($login_history as $log): ?>
                    <tr>
                        <?php if ($is_admin): ?>
                        <td>
                            <strong><?php echo $log['username']; ?></strong>
                        </td>
                        <?php endif; ?>
                        <td><?php echo date('d M Y H:i:s', strtotime($log['login_time'])); ?></td>
                        <td><?php echo $log['ip_address']; ?></td>
                        <td>
                            <span class="device-badge <?php echo strtolower($log['device_type']); ?>">
                                <i class="fas fa-<?php echo $log['device_type'] == 'Mobile' ? 'mobile-alt' : ($log['device_type'] == 'Tablet' ? 'tablet-alt' : 'desktop'); ?>"></i>
                                <?php echo $log['device_type']; ?>
                            </span>
                        </td>
                        <td><?php echo $log['browser']; ?> on <?php echo $log['os']; ?></td>
                        <td><?php echo $log['location']; ?></td>
                        <td>
                            <span class="status-badge <?php echo $log['status']; ?>">
                                <?php echo ucfirst($log['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Security Alerts -->
        <div class="section">
            <h2>
                <span><i class="fas fa-exclamation-triangle"></i> Security Alerts</span>
            </h2>
            
            <table>
                <thead>
                    <tr>
                        <?php if ($is_admin): ?>
                        <th>User</th>
                        <?php endif; ?>
                        <th>Type</th>
                        <th>Message</th>
                        <th>IP Address</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alerts as $alert): ?>
                    <tr>
                        <?php if ($is_admin): ?>
                        <td><?php echo $alert['username']; ?></td>
                        <?php endif; ?>
                        <td>
                            <span class="device-badge">
                                <?php echo ucfirst(str_replace('_', ' ', $alert['alert_type'])); ?>
                            </span>
                        </td>
                        <td><?php echo $alert['message']; ?></td>
                        <td><?php echo $alert['ip_address']; ?></td>
                        <td><?php echo date('d M Y H:i:s', strtotime($alert['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Known Devices -->
        <div class="section">
            <h2>
                <span><i class="fas fa-laptop"></i> Known Devices</span>
            </h2>
            
            <table>
                <thead>
                    <tr>
                        <?php if ($is_admin): ?>
                        <th>User</th>
                        <?php endif; ?>
                        <th>Device</th>
                        <th>Fingerprint</th>
                        <th>Last Seen</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($devices as $device): ?>
                    <tr>
                        <?php if ($is_admin): ?>
                        <td><?php echo $device['username']; ?></td>
                        <?php endif; ?>
                        <td><?php echo $device['device_name']; ?></td>
                        <td><code><?php echo substr($device['device_fingerprint'], 0, 16); ?>...</code></td>
                        <td><?php echo date('d M Y H:i:s', strtotime($device['last_seen'])); ?></td>
                        <td>
                            <?php if ($device['is_trusted']): ?>
                                <span class="status-badge success">Trusted</span>
                            <?php else: ?>
                                <span class="status-badge failed">New Device</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Live Clock -->
    <div class="live-clock" id="live-clock">
        <i class="fas fa-clock"></i> Loading...
    </div>
    
    <!-- Footer Note -->
    <div class="footer-note">
        <i class="fas fa-shield-alt"></i> Secure Security Panel • Real-time Monitoring
    </div>
    
    <!-- Block User Modal (Admin Only) -->
    <div class="modal" id="blockModal">
        <div class="modal-content">
            <h3><i class="fas fa-ban" style="color:#dc3545;"></i> Block User</h3>
            <p>Blocking <strong id="blockUsername"></strong> will prevent them from logging in.</p>
            
            <form method="POST" id="blockForm">
                <input type="hidden" name="action" value="block_user">
                <input type="hidden" name="user_id" id="blockUserId">
                
                <label>Reason for blocking:</label>
                <textarea name="reason" placeholder="Enter reason..." rows="3" required></textarea>
                
                <div class="modal-buttons">
                    <button type="submit" class="block-btn">
                        <i class="fas fa-ban"></i> Block User
                    </button>
                    <button type="button" class="unblock-btn" onclick="closeBlockModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function toggleMenu() {
            const menu = document.getElementById("menu");
            menu.classList.toggle('show');
        }

        // Close menu when clicking outside
        document.addEventListener("click", function(e){
            const menu = document.getElementById("menu");
            const dots = document.querySelector(".three-dots");
            
            if(!menu.contains(e.target) && !dots.contains(e.target)){
                menu.classList.remove('show');
            }
        });
        
        function showBlockModal(userId, username) {
            document.getElementById('blockModal').classList.add('active');
            document.getElementById('blockUserId').value = userId;
            document.getElementById('blockUsername').textContent = username;
        }
        
        function closeBlockModal() {
            document.getElementById('blockModal').classList.remove('active');
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('blockModal');
            if (event.target == modal) {
                modal.classList.remove('active');
            }
        }
        
        // Live clock function
        function updateClock() {
            const now = new Date();
            const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('live-clock').innerHTML = '<i class="fas fa-clock"></i> ' + time;
        }
        setInterval(updateClock, 1000);
        
        // Highlight current page in dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = 'security.php';
            const menuLinks = document.querySelectorAll('.dropdown a');
            menuLinks.forEach(link => {
                if(link.getAttribute('href') === currentPage) {
                    link.style.background = '#3282b8';
                    link.style.paddingLeft = '25px';
                }
            });
        });
    </script>
</body>
</html>
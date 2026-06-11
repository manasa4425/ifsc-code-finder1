<?php
// settings.php - User Profile & Security Settings (Admin gets extra options)
session_start();
require_once 'db_config.php';

// FIX: Check for ANY type of login session
$is_logged_in = false;
$user_id = null;
$username = null;
$full_name = null;
$user_role = 'guest';

// Check different session types
if (isset($_SESSION['user_id'])) {
    // Regular user from users table
    $is_logged_in = true;
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'] ?? '';
    $full_name = $_SESSION['full_name'] ?? '';
    $user_role = 'user';
    
    // Get user details from users table
    $user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
    if ($user_query && mysqli_num_rows($user_query) > 0) {
        $user = mysqli_fetch_assoc($user_query);
        $user_role = $user['role'] ?? 'user';
    }
} 
elseif (isset($_SESSION['accno'])) {
    // Customer from account table
    $is_logged_in = true;
    $user_id = $_SESSION['accno'];
    $username = $_SESSION['accno'];
    $full_name = $_SESSION['name'] ?? 'Customer';
    $user_role = 'customer';
    
    // Get customer details from account table
    $user_query = mysqli_query($conn, "SELECT * FROM account WHERE accno = '$user_id'");
    if ($user_query && mysqli_num_rows($user_query) > 0) {
        $user = mysqli_fetch_assoc($user_query);
    } else {
        $user = ['username' => $user_id, 'full_name' => $full_name, 'email' => '', 'phone' => '', 'created_at' => date('Y-m-d'), 'last_login' => null, 'role' => 'customer'];
    }
} 
elseif (isset($_SESSION['bname'])) {
    // Bank from bank login
    $is_logged_in = true;
    $user_id = $_SESSION['bname'];
    $username = $_SESSION['bname'];
    $full_name = $_SESSION['bname'] . ' Bank';
    $user_role = 'bank';
    
    // Bank user data
    $user = [
        'username' => $user_id,
        'full_name' => $full_name,
        'email' => 'bank@example.com',
        'phone' => '',
        'created_at' => date('Y-m-d'),
        'last_login' => null,
        'role' => 'bank',
        'two_factor_enabled' => 0,
        'biometric_enabled' => 0,
        'password' => ''
    ];
} 
elseif (isset($_SESSION['admin'])) {
    // Admin from admin login
    $is_logged_in = true;
    $user_id = $_SESSION['admin'];
    $username = $_SESSION['admin'];
    $full_name = 'Administrator';
    $user_role = 'admin';
    
    // Admin user data
    $user = [
        'username' => $user_id,
        'full_name' => 'Administrator',
        'email' => 'admin@bankifsc.com',
        'phone' => '',
        'created_at' => date('Y-m-d'),
        'last_login' => null,
        'role' => 'admin',
        'two_factor_enabled' => 0,
        'biometric_enabled' => 0,
        'password' => ''
    ];
}

// If not logged in at all, redirect to homepage
if (!$is_logged_in) {
    header("Location: index.php");  // FIX: Changed from login1.php to index.php
    exit();
}

// Check if current user is ADMIN (for admin features)
$is_admin = ($user_role === 'admin');

// Get unread security alerts count (only for regular users)
$alert_count = 0;
if (isset($_SESSION['user_id'])) {
    $alerts_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM security_alerts WHERE user_id = $user_id AND is_read = FALSE");
    if ($alerts_query) {
        $alert_count = mysqli_fetch_assoc($alerts_query)['count'];
    }
}

// Handle profile update
$profile_message = '';
if (isset($_POST['update_profile']) && $user_role != 'guest') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    if ($user_role == 'user') {
        // Update users table
        $update = mysqli_query($conn, "UPDATE users SET 
                                       full_name = '$full_name',
                                       email = '$email',
                                       phone = '$phone' 
                                       WHERE id = $user_id");
        if ($update) {
            $_SESSION['full_name'] = $full_name;
            $profile_message = "<div class='alert success'>Profile updated successfully!</div>";
        }
    } elseif ($user_role == 'customer') {
        // Update account table
        $update = mysqli_query($conn, "UPDATE account SET 
                                       name = '$full_name',
                                       email = '$email',
                                       phone = '$phone' 
                                       WHERE accno = '$user_id'");
        if ($update) {
            $_SESSION['name'] = $full_name;
            $profile_message = "<div class='alert success'>Profile updated successfully!</div>";
        }
    }
    
    // Refresh user data
    if ($user_role == 'user') {
        $user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
        $user = mysqli_fetch_assoc($user_query);
    } elseif ($user_role == 'customer') {
        $user_query = mysqli_query($conn, "SELECT * FROM account WHERE accno = '$user_id'");
        $user = mysqli_fetch_assoc($user_query);
    }
}

// Handle password change
$password_message = '';
if (isset($_POST['change_password']) && $user_role != 'guest') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    
    // Get current password from appropriate table
    $current_password = '';
    if ($user_role == 'user') {
        $current_password = $user['password'] ?? '';
    } elseif ($user_role == 'customer') {
        $current_password = $user['password'] ?? '';
    }
    
    // Verify current password
    if ($current != $current_password) {
        $password_message = "<div class='alert error'>Current password is incorrect!</div>";
    } elseif ($new != $confirm) {
        $password_message = "<div class='alert error'>New passwords do not match!</div>";
    } elseif (strlen($new) < 6) {
        $password_message = "<div class='alert error'>Password must be at least 6 characters!</div>";
    } else {
        if ($user_role == 'user') {
            $update = mysqli_query($conn, "UPDATE users SET password = '$new' WHERE id = $user_id");
        } elseif ($user_role == 'customer') {
            $update = mysqli_query($conn, "UPDATE account SET password = '$new' WHERE accno = '$user_id'");
        }
        
        if ($update) {
            $password_message = "<div class='alert success'>Password changed successfully!</div>";
        } else {
            $password_message = "<div class='alert error'>Password change failed!</div>";
        }
    }
}

// Handle 2FA toggle (only for regular users)
$twofa_message = '';
if (isset($_POST['toggle_2fa']) && $user_role == 'user') {
    $current_status = $user['two_factor_enabled'] ?? 0;
    $new_status = $current_status ? 0 : 1;
    
    $update = mysqli_query($conn, "UPDATE users SET two_factor_enabled = $new_status WHERE id = $user_id");
    if ($update) {
        $twofa_message = "<div class='alert success'>2FA " . ($new_status ? 'enabled' : 'disabled') . " successfully!</div>";
        // Refresh user data
        $user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
        $user = mysqli_fetch_assoc($user_query);
    }
}

// Handle biometric toggle (only for regular users)
$bio_message = '';
if (isset($_POST['toggle_biometric']) && $user_role == 'user') {
    $current_status = $user['biometric_enabled'] ?? 0;
    $new_status = $current_status ? 0 : 1;
    
    $update = mysqli_query($conn, "UPDATE users SET biometric_enabled = $new_status WHERE id = $user_id");
    if ($update) {
        $bio_message = "<div class='alert success'>Biometric " . ($new_status ? 'enabled' : 'disabled') . " successfully!</div>";
        // Refresh user data
        $user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
        $user = mysqli_fetch_assoc($user_query);
    }
}

// ADMIN ONLY: Get all users for management
$all_users = [];
if ($is_admin) {
    $users_query = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
    if ($users_query) {
        $all_users = mysqli_fetch_all($users_query, MYSQLI_ASSOC);
    }
}

// ADMIN ONLY: Handle user role update
$admin_message = '';
if ($is_admin && isset($_POST['update_user_role'])) {
    $target_user_id = (int)$_POST['user_id'];
    $new_role = mysqli_real_escape_string($conn, $_POST['role']);
    
    // Don't allow admin to demote themselves
    if ($target_user_id != $user_id) {
        $update = mysqli_query($conn, "UPDATE users SET role = '$new_role' WHERE id = $target_user_id");
        if ($update) {
            $admin_message = "<div class='alert success'>User role updated to $new_role!</div>";
        }
    }
}

// ADMIN ONLY: Handle user deletion
if ($is_admin && isset($_POST['admin_delete_user'])) {
    $target_user_id = (int)$_POST['delete_user_id'];
    
    // Don't allow admin to delete themselves
    if ($target_user_id != $user_id) {
        mysqli_query($conn, "DELETE FROM login_history WHERE user_id = $target_user_id");
        mysqli_query($conn, "DELETE FROM security_alerts WHERE user_id = $target_user_id");
        mysqli_query($conn, "DELETE FROM known_devices WHERE user_id = $target_user_id");
        mysqli_query($conn, "DELETE FROM users WHERE id = $target_user_id");
        $admin_message = "<div class='alert success'>User deleted successfully!</div>";
    }
}

// ADMIN ONLY: System stats
$system_stats = [];
if ($is_admin) {
    $system_stats['total_users'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'] ?? 0;
    $system_stats['total_logins'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM login_history"))['count'] ?? 0;
    $system_stats['total_alerts'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM security_alerts"))['count'] ?? 0;
    $system_stats['total_devices'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM known_devices"))['count'] ?? 0;
    $system_stats['active_today'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT user_id) as count FROM login_history WHERE DATE(login_time) = CURDATE()"))['count'] ?? 0;
}

// Handle account deletion (for regular users)
$delete_message = '';
if (!$is_admin && isset($_POST['delete_account'])) {
    $confirm_text = $_POST['confirm_delete'];
    
    if ($confirm_text === 'DELETE') {
        if ($user_role == 'user') {
            // Delete user's data
            mysqli_query($conn, "DELETE FROM login_history WHERE user_id = $user_id");
            mysqli_query($conn, "DELETE FROM security_alerts WHERE user_id = $user_id");
            mysqli_query($conn, "DELETE FROM known_devices WHERE user_id = $user_id");
            mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");
        } elseif ($user_role == 'customer') {
            // Delete customer's data
            mysqli_query($conn, "DELETE FROM transactions WHERE accountno = '$user_id'");
            mysqli_query($conn, "DELETE FROM account WHERE accno = '$user_id'");
        }
        
        session_destroy();
        header("Location: index.php?deleted=1");
        exit();
    } else {
        $delete_message = "<div class='alert error'>Please type DELETE to confirm</div>";
    }
}

// Get device for biometric suggestion
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$is_mobile = preg_match('/(android|iphone|ipad|mobile)/i', $user_agent);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Bank IFSC Finder</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* ALL YOUR EXISTING STYLES REMAIN EXACTLY THE SAME */
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
            max-width: 1200px;
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
        .user-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .badge-admin {
            background: #dc3545;
            color: white;
        }
        
        .badge-user {
            background: #28a745;
            color: white;
        }
        
        .admin-section {
            border: 2px solid #dc3545;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            background: linear-gradient(135deg, #fff5f5, #ffffff);
            position: relative;
        }
        
        .admin-section::before {
            content: '🔒 ADMIN ONLY';
            position: absolute;
            top: -12px;
            left: 20px;
            background: #dc3545;
            color: white;
            padding: 2px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .admin-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            color: #dc3545;
        }
        
        .admin-header i {
            font-size: 24px;
        }
        
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 25px;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        
        .card h2 i {
            color: #667eea;
            margin-right: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #666;
            font-weight: 500;
        }
        
        .form-group label i {
            color: #667eea;
            margin-right: 5px;
            width: 20px;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.3s;
        }
        
        .form-group input:focus, .form-group select:focus {
            border-color: #667eea;
            outline: none;
        }
        
        .form-group input[readonly] {
            background: #f8f9fa;
            color: #666;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
            width: 100%;
        }
        
        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-admin {
            background: #dc3545;
            color: white;
        }
        
        .btn-admin:hover {
            background: #c82333;
        }
        
        .toggle-switch {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        
        .toggle-info {
            flex: 1;
        }
        
        .toggle-info h4 {
            color: #333;
            margin-bottom: 5px;
        }
        
        .toggle-info p {
            color: #666;
            font-size: 13px;
        }
        
        .toggle-status {
            font-size: 14px;
            font-weight: 600;
            padding: 5px 15px;
            border-radius: 20px;
        }
        
        .status-enabled {
            background: #d4edda;
            color: #155724;
        }
        
        .status-disabled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 14px;
            color: #333;
        }
        
        .info-box i {
            color: #667eea;
            margin-right: 8px;
        }
        
        .delete-section {
            background: #fff5f5;
            border-left: 4px solid #dc3545;
            padding: 20px;
            border-radius: 10px;
        }
        
        .delete-section h3 {
            color: #dc3545;
            margin-bottom: 10px;
        }
        
        .delete-section p {
            color: #666;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .alert i {
            font-size: 18px;
        }
        
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-card .number {
            font-size: 28px;
            font-weight: bold;
        }
        
        .stat-card .label {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .user-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
        }
        
        .user-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .user-table tr:hover {
            background: #f8f9fa;
        }
        
        .role-select {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
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
            .settings-grid {
                grid-template-columns: 1fr;
            }
            
            .row {
                grid-template-columns: 1fr;
            }
            
            .user-table {
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
        <?php if ($is_logged_in): ?>
        <div class="user-info">
            <div class="user-details">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($full_name ?: $username); ?></span>
            </div>
            
            <?php if ($user_role == 'user'): ?>
            <a href="security.php" class="security-link">
                <i class="fas fa-shield-alt"></i> <span>Security</span>
                <?php if ($alert_count > 0): ?>
                    <span class="alert-badge"><?php echo $alert_count; ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>
            
            <a href="logout2.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
        <?php endif; ?>

        <!-- Dropdown Menu -->
        <div class="dropdown" id="menu">
            <!-- Show user info in dropdown if logged in -->
            <?php if ($is_logged_in): ?>
            <div class="dropdown-user">
                <div class="user-name"><?php echo htmlspecialchars($full_name ?: $username); ?></div>
                <div class="user-email">Logged in as <?php echo $user_role; ?></div>
            </div>
            <?php endif; ?>
            
            <a href="index.php"><i class="fa fa-home"></i> Home</a>
            <?php if ($user_role == 'user'): ?>
            <a href="security.php">
                <i class="fa fa-shield-halved"></i> Security Panel
                <?php if ($alert_count > 0): ?>
                    <span style="background:#DC2626;color:white;padding:2px 8px;border-radius:10px;margin-left:5px;">
                        <?php echo $alert_count; ?>
                    </span>
                <?php endif; ?>
            </a>
            <?php endif; ?>
            <a href="about.php"><i class="fa fa-info-circle"></i> About</a>
            <a href="adminlogin.php"><i class="fa fa-user-shield"></i> Admin Login</a>
            <a href="banklogin.php"><i class="fa fa-university"></i> Bank Login</a>
            <a href="customerlogin.php"><i class="fa fa-users"></i> Customer Login</a>
            <a href="bankdetails.php"><i class="fa fa-building-columns"></i> Bank Details</a>
            <a href="contact.php"><i class="fa fa-phone"></i> Contact</a>
            <a href="settings.php"><i class="fa fa-gear"></i> Settings</a>
            
            <!-- Show logout in dropdown if user is logged in -->
            <?php if ($is_logged_in): ?>
            <a href="logout2.php" style="background:#DC2626;"><i class="fa fa-sign-out-alt"></i> Logout</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container">
        <!-- Welcome message for admin -->
        <?php if ($is_admin): ?>
        <div class="alert success" style="background: #dc3545; color: white; border-left: 4px solid #fff;">
            <i class="fas fa-crown"></i> 
            <strong>Welcome Admin!</strong> You have access to advanced system controls.
        </div>
        <?php endif; ?>
        
        <?php echo $admin_message; ?>
        <?php echo $profile_message; ?>
        <?php echo $password_message; ?>
        <?php echo $twofa_message; ?>
        <?php echo $bio_message; ?>
        
        <!-- ADMIN ONLY SECTION - Only visible to admin -->
        <?php if ($is_admin): ?>
        <div class="admin-section">
            <div class="admin-header">
                <i class="fas fa-crown"></i>
                <h2>System Administration</h2>
            </div>
            
            <!-- System Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number"><?php echo $system_stats['total_users']; ?></div>
                    <div class="label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo $system_stats['total_logins']; ?></div>
                    <div class="label">Total Logins</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo $system_stats['total_alerts']; ?></div>
                    <div class="label">Security Alerts</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?php echo $system_stats['active_today']; ?></div>
                    <div class="label">Active Today</div>
                </div>
            </div>
            
            <!-- User Management Table -->
            <h3 style="margin: 20px 0 15px; color: #333;">
                <i class="fas fa-users"></i> Manage Users
            </h3>
            
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>2FA</th>
                        <th>Bio</th>
                        <th>Actions</th>
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
                            <span class="user-badge <?php echo ($u['role'] ?? 'user') == 'admin' ? 'badge-admin' : 'badge-user'; ?>" style="margin:0;">
                                <?php echo ucfirst($u['role'] ?? 'user'); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($u['two_factor_enabled'])): ?>
                                <i class="fas fa-check-circle" style="color:#28a745;"></i>
                            <?php else: ?>
                                <i class="fas fa-times-circle" style="color:#dc3545;"></i>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($u['biometric_enabled'])): ?>
                                <i class="fas fa-check-circle" style="color:#28a745;"></i>
                            <?php else: ?>
                                <i class="fas fa-times-circle" style="color:#dc3545;"></i>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($u['id'] != $user_id): ?>
                            <div style="display: flex; gap: 5px;">
                                <!-- Role Update Form -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <select name="role" class="role-select" onchange="this.form.submit()">
                                        <option value="user" <?php echo ($u['role'] ?? 'user') == 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="admin" <?php echo ($u['role'] ?? '') == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        <option value="blocked" <?php echo ($u['role'] ?? '') == 'blocked' ? 'selected' : ''; ?>>Blocked</option>
                                    </select>
                                    <input type="hidden" name="update_user_role" value="1">
                                </form>
                                
                                <!-- Delete User Form -->
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete user <?php echo $u['username']; ?>?');">
                                    <input type="hidden" name="delete_user_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" name="admin_delete_user" class="btn-danger" style="padding:5px 10px; font-size:12px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                                <span style="color:#999; font-size:12px;">(You)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="info-box" style="margin-top:20px;">
                <i class="fas fa-info-circle"></i>
                <strong>Admin Note:</strong> You can change user roles or delete users. You cannot delete or demote yourself.
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Settings Grid (Visible to ALL logged in users) -->
        <div class="settings-grid">
            <!-- Left Column - Profile Info (All logged in users) -->
            <div class="card">
                <h2><i class="fas fa-user-circle"></i> Profile Information</h2>
                
                <form method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Username / Account</label>
                        <input type="text" value="<?php echo $username; ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> Full Name</label>
                        <input type="text" name="full_name" value="<?php echo $full_name; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" value="<?php echo $user['email'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="text" name="phone" value="<?php echo $user['phone'] ?? ''; ?>" required>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>
            
            <!-- Right Column - Security Settings (All logged in users) -->
            <div class="card">
                <h2><i class="fas fa-lock"></i> Security Settings</h2>
                
                <form method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-key"></i> Current Password</label>
                        <input type="password" name="current_password" placeholder="Enter current password" required>
                    </div>
                    
                    <div class="row">
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> New Password</label>
                            <input type="password" name="new_password" placeholder="New password" required>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-check-circle"></i> Confirm</label>
                            <input type="password" name="confirm_password" placeholder="Confirm password" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="change_password" class="btn btn-primary">
                        <i class="fas fa-sync-alt"></i> Change Password
                    </button>
                </form>
                
                <!-- Two-Factor Authentication Toggle (Only for regular users) -->
                <?php if ($user_role == 'user'): ?>
                <div class="toggle-switch" style="margin-top:20px;">
                    <div class="toggle-info">
                        <h4><i class="fas fa-mobile-alt"></i> Two-Factor Authentication</h4>
                        <p>Add an extra layer of security to your account</p>
                    </div>
                    <div>
                        <span class="toggle-status <?php echo !empty($user['two_factor_enabled']) ? 'status-enabled' : 'status-disabled'; ?>">
                            <?php echo !empty($user['two_factor_enabled']) ? 'Enabled' : 'Disabled'; ?>
                        </span>
                    </div>
                </div>
                
                <form method="POST">
                    <button type="submit" name="toggle_2fa" class="btn <?php echo !empty($user['two_factor_enabled']) ? 'btn-warning' : 'btn-success'; ?>" style="width:100%;">
                        <i class="fas fa-<?php echo !empty($user['two_factor_enabled']) ? 'times' : 'check'; ?>"></i>
                        <?php echo !empty($user['two_factor_enabled']) ? 'Disable 2FA' : 'Enable 2FA'; ?>
                    </button>
                </form>
                
                <!-- Biometric Authentication Toggle (Only for regular users) -->
                <div class="toggle-switch" style="margin-top:20px;">
                    <div class="toggle-info">
                        <h4><i class="fas fa-fingerprint"></i> Biometric Login</h4>
                        <p>
                            <?php if ($is_mobile): ?>
                            Use fingerprint or face recognition
                            <?php else: ?>
                            Use Windows Hello on desktop
                            <?php endif; ?>
                        </p>
                    </div>
                    <div>
                        <span class="toggle-status <?php echo !empty($user['biometric_enabled']) ? 'status-enabled' : 'status-disabled'; ?>">
                            <?php echo !empty($user['biometric_enabled']) ? 'Enabled' : 'Disabled'; ?>
                        </span>
                    </div>
                </div>
                
                <form method="POST">
                    <button type="submit" name="toggle_biometric" class="btn <?php echo !empty($user['biometric_enabled']) ? 'btn-warning' : 'btn-success'; ?>" style="width:100%;">
                        <i class="fas fa-<?php echo !empty($user['biometric_enabled']) ? 'times' : 'fingerprint'; ?>"></i>
                        <?php echo !empty($user['biometric_enabled']) ? 'Disable Biometric' : 'Enable Biometric'; ?>
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Account Information Card (All logged in users) -->
        <div class="card" style="margin-top:25px;">
            <h2><i class="fas fa-info-circle"></i> Account Information</h2>
            
            <div class="row">
                <div class="form-group">
                    <label><i class="fas fa-calendar-alt"></i> Member Since</label>
                    <input type="text" value="<?php echo isset($user['created_at']) ? date('d F Y', strtotime($user['created_at'])) : date('d F Y'); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Last Login</label>
                    <input type="text" value="<?php echo isset($user['last_login']) && $user['last_login'] ? date('d F Y, h:i A', strtotime($user['last_login'])) : 'Never'; ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> Account ID</label>
                    <input type="text" value="<?php echo '#' . $user_id; ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-shield-alt"></i> Account Type</label>
                    <input type="text" value="<?php echo ucfirst($user_role); ?>" readonly>
                </div>
            </div>
        </div>
        
        <!-- Danger Zone - Only for Regular Users (NOT admin) -->
        <?php if (!$is_admin && $user_role != 'bank'): ?>
        <div class="delete-section" style="margin-top:25px;">
            <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
            <p>Once you delete your account, there is no going back. Please be certain.</p>
            
            <?php echo $delete_message; ?>
            
            <form method="POST" onsubmit="return confirm('Are you absolutely sure? This action cannot be undone!');">
                <div class="row" style="align-items: center;">
                    <div class="form-group" style="margin:0;">
                        <input type="text" name="confirm_delete" placeholder="Type DELETE to confirm" required style="margin:0;">
                    </div>
                    <button type="submit" name="delete_account" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Account
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- Security Score (All logged in users) -->
        <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 15px; margin-top: 25px; color: white;">
            <div style="display: flex; gap: 20px; flex-wrap: wrap; justify-content: center;">
                <span><i class="fas fa-check-circle" style="color:#28a745;"></i> Strong Password</span>
                <?php if ($user_role == 'user'): ?>
                <span><i class="fas fa-<?php echo !empty($user['two_factor_enabled']) ? 'check-circle' : 'times-circle'; ?>" style="color:<?php echo !empty($user['two_factor_enabled']) ? '#28a745' : '#dc3545'; ?>;"></i> 2FA <?php echo !empty($user['two_factor_enabled']) ? 'Enabled' : 'Disabled'; ?></span>
                <span><i class="fas fa-<?php echo !empty($user['biometric_enabled']) ? 'check-circle' : 'times-circle'; ?>" style="color:<?php echo !empty($user['biometric_enabled']) ? '#28a745' : '#dc3545'; ?>;"></i> Biometric <?php echo !empty($user['biometric_enabled']) ? 'Enabled' : 'Disabled'; ?></span>
                <?php endif; ?>
                <span><i class="fas fa-shield-alt" style="color:#667eea;"></i> Account Type: <?php echo ucfirst($user_role); ?></span>
            </div>
        </div>
    </div>
    
    <!-- Live Clock -->
    <div class="live-clock" id="live-clock">
        <i class="fas fa-clock"></i> Loading...
    </div>
    
    <!-- Footer Note -->
    <div class="footer-note">
        <i class="fas fa-shield-alt"></i> Secure Settings • End-to-End Encrypted
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
        
        // Password strength indicator
        document.querySelector('input[name="new_password"]')?.addEventListener('input', function(e) {
            const password = e.target.value;
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[$@#&!]+/)) strength++;
            
            const strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            const strengthColor = ['#dc3545', '#dc3545', '#ffc107', '#17a2b8', '#28a745'];
            
            let indicator = document.getElementById('strength-indicator');
            if (!indicator) {
                indicator = document.createElement('small');
                indicator.id = 'strength-indicator';
                indicator.style.marginTop = '5px';
                indicator.style.display = 'block';
                e.target.parentNode.appendChild(indicator);
            }
            
            if (password.length > 0) {
                indicator.innerHTML = 'Strength: ' + strengthText[strength];
                indicator.style.color = strengthColor[strength];
            } else {
                indicator.innerHTML = '';
            }
        });
        
        // Live clock function
        function updateClock() {
            const now = new Date();
            const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('live-clock').innerHTML = '<i class="fas fa-clock"></i> ' + time;
        }
        setInterval(updateClock, 1000);
        
        // Highlight current page in dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = 'settings.php';
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
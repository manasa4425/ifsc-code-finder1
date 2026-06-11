<?php
// bankdetails.php
// Professional Bank IFSC Finder - Enhanced with Download Options for Transaction Histories

// Enable error reporting for development (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session for storing user location/preferences
session_start();

// Include database connection from alldeatils.php
include("db.php");

// Database configuration (simulated - in production use real DB)
define('DB_HOST', 'localhost');
define('DB_NAME', 'bank_ifsc');
define('DB_USER', 'root');
define('DB_PASS', '');

// For showing username in header (if any user is logged in from main site)
$username = '';
$full_name = '';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'] ?? '';
    $full_name = $_SESSION['full_name'] ?? '';
}

// Get unread security alerts count (if user is logged in)
$alert_count = 0;
if (isset($_SESSION['user_id'])) {
    $alerts_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM security_alerts WHERE user_id = $user_id AND is_read = FALSE");
    if ($alerts_query) {
        $alert_count = mysqli_fetch_assoc($alerts_query)['count'];
    }
}

// Function to get deposit history
function getDepositHistory($conn, $limit = 10) {
    $query = "SELECT * FROM transactions WHERE deposite > 0 ORDER BY id DESC LIMIT $limit";
    $result = mysqli_query($conn, $query);
    $data = [];
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Function to get withdraw history
function getWithdrawHistory($conn, $limit = 10) {
    $query = "SELECT * FROM transactions WHERE withdraw > 0 ORDER BY id DESC LIMIT $limit";
    $result = mysqli_query($conn, $query);
    $data = [];
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Function to get transfer history
function getTransferHistory($conn, $limit = 10) {
    $query = "SELECT * FROM transactions WHERE transfer_from != '' AND transfer_to != '' ORDER BY id DESC LIMIT $limit";
    $result = mysqli_query($conn, $query);
    $data = [];
    while($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Get totals for display
$total_deposit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(deposite) as total FROM transactions"))['total'] ?? 0;
$total_withdraw = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(withdraw) as total FROM transactions"))['total'] ?? 0;
$total_transfer = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transactions WHERE transfer_from != ''"));

// Handle Download Requests
if (isset($_GET['download'])) {
    $type = $_GET['download'];
    $format = $_GET['format'] ?? 'csv';
    
    switch($type) {
        case 'deposit':
            $data = getDepositHistory($conn, 1000);
            $filename = "deposit_history_" . date('Y-m-d') . "." . $format;
            $headers = ['ID', 'Date', 'Account No', 'Amount', 'Description'];
            $rows = [];
            foreach($data as $row) {
                $rows[] = [
                    $row['id'],
                    $row['date'] ?? date('Y-m-d H:i:s'),
                    $row['accountno'],
                    $row['deposite'],
                    'Deposit'
                ];
            }
            break;
            
        case 'withdraw':
            $data = getWithdrawHistory($conn, 1000);
            $filename = "withdraw_history_" . date('Y-m-d') . "." . $format;
            $headers = ['ID', 'Date', 'Account No', 'Amount', 'Description'];
            $rows = [];
            foreach($data as $row) {
                $rows[] = [
                    $row['id'],
                    $row['date'] ?? date('Y-m-d H:i:s'),
                    $row['accountno'],
                    $row['withdraw'],
                    'Withdrawal'
                ];
            }
            break;
            
        case 'transfer':
            $data = getTransferHistory($conn, 1000);
            $filename = "transfer_history_" . date('Y-m-d') . "." . $format;
            $headers = ['ID', 'Date', 'From Account', 'To Account', 'Amount', 'Description'];
            $rows = [];
            foreach($data as $row) {
                $rows[] = [
                    $row['id'],
                    $row['date'] ?? date('Y-m-d H:i:s'),
                    $row['transfer_from'],
                    $row['transfer_to'],
                    $row['amount'],
                    'Transfer'
                ];
            }
            break;
    }
    
    // Output based on format
    if ($format == 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        foreach($rows as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
        
    } elseif ($format == 'pdf') {
        // For PDF, we'll use HTML table and convert
        header('Content-Type: text/html');
        echo "<html><head><title>$filename</title>";
        echo "<style>table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } th { background-color: #667eea; color: white; }</style>";
        echo "</head><body>";
        echo "<h2>" . ucfirst($type) . " History</h2>";
        echo "<table>";
        echo "<tr>";
        foreach($headers as $header) {
            echo "<th>$header</th>";
        }
        echo "</tr>";
        foreach($rows as $row) {
            echo "<tr>";
            foreach($row as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        echo "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>";
        echo "</body></html>";
        exit;
    }
}

// Simulated bank branch data functions (from your existing code)
function getBankDetails($ifsc) {
    // Your existing bank details function
    $branches = [
        'SBIN0012345' => [
            'bank' => 'State Bank of India',
            'branch' => 'Connaught Place',
            'ifsc' => 'SBIN0012345',
            'micr' => '110002123',
            'address' => 'Jeevan Bharati Building, Tower 1, Connaught Place',
            'city' => 'New Delhi',
            'district' => 'New Delhi',
            'state' => 'Delhi',
            'pincode' => '110001',
            'contact' => '011-23456789',
            'fax' => '011-23456788',
            'email' => 'cp.branch@sbi.co.in',
            'latitude' => 28.6328,
            'longitude' => 77.2197,
            'rating' => 4.3,
            'total_ratings' => 128,
            'established' => '1955',
            'monday_friday' => '10:00 AM – 4:00 PM',
            'saturday' => '10:00 AM – 2:00 PM',
            'sunday' => 'Closed',
            'holidays' => '2nd & 4th Saturday, National Holidays',
            'facilities' => ['ATM', 'Cash Deposit', 'Passbook Printer', 'Wheelchair Access'],
            'services' => ['NEFT', 'RTGS', 'IMPS', 'UPI', 'Demand Draft'],
            'manager' => 'Mr. Rajesh Kumar',
            'category' => 'Public Sector Bank'
        ],
        'HDFC0001234' => [
            'bank' => 'HDFC Bank',
            'branch' => 'Bandra West',
            'ifsc' => 'HDFC0001234',
            'micr' => '400240123',
            'address' => 'Shop No. 5-7, Ground Floor, Imperial Heights, Linking Road, Bandra West',
            'city' => 'Mumbai',
            'district' => 'Mumbai Suburban',
            'state' => 'Maharashtra',
            'pincode' => '400050',
            'contact' => '022-26547896',
            'fax' => '022-26547895',
            'email' => 'bandra.branch@hdfcbank.com',
            'latitude' => 19.0596,
            'longitude' => 72.8295,
            'rating' => 4.5,
            'total_ratings' => 256,
            'established' => '1998',
            'monday_friday' => '09:30 AM – 3:30 PM',
            'saturday' => '09:30 AM – 1:30 PM',
            'sunday' => 'Closed',
            'holidays' => '2nd & 4th Saturday, Sunday, National Holidays',
            'facilities' => ['ATM', 'Cash Deposit Machine', 'Digital Kiosk', 'Parking', 'Wheelchair'],
            'services' => ['NEFT', 'RTGS', 'IMPS', 'UPI', 'Foreign Exchange', 'Lockers'],
            'manager' => 'Ms. Priya Sharma',
            'category' => 'Private Sector Bank'
        ]
    ];
    
    return isset($branches[$ifsc]) ? $branches[$ifsc] : $branches['SBIN0012345'];
}

function getNearestBranches($currentLat, $currentLng, $currentIfsc, $limit = 5) {
    // Your existing nearest branches function
    $allBranches = [
        [
            'bank' => 'Axis Bank',
            'branch' => 'CP Extension',
            'ifsc' => 'UTIB0004321',
            'address' => 'F-19, Inner Circle, Connaught Place',
            'distance' => '0.3 km',
            'latitude' => 28.6340,
            'longitude' => 77.2180,
            'rating' => 4.0
        ],
        [
            'bank' => 'Kotak Mahindra Bank',
            'branch' => 'Barakhamba Road',
            'ifsc' => 'KKBK0001234',
            'address' => 'G-4, Meridian Building, Barakhamba Road',
            'distance' => '0.7 km',
            'latitude' => 28.6310,
            'longitude' => 77.2230,
            'rating' => 4.2
        ]
    ];
    
    return $allBranches;
}

// Get IFSC from URL parameter
$ifsc = isset($_GET['ifsc']) ? strtoupper(trim($_GET['ifsc'])) : 'SBIN0012345';
$branchData = getBankDetails($ifsc);
$nearestBranches = getNearestBranches($branchData['latitude'], $branchData['longitude'], $ifsc);

// Generate star rating display
function displayStars($rating) {
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $stars = '';
    for ($i = 1; $i <= $full; $i++) $stars .= '⭐';
    if ($half) $stars .= '<span class="half-star">✨</span>';
    $empty = 5 - $full - $half;
    for ($i = 1; $i <= $empty; $i++) $stars .= '☆';
    return $stars;
}

$mapQuery = urlencode($branchData['address'] . ', ' . $branchData['city'] . ', ' . $branchData['state'] . ' - ' . $branchData['pincode']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($branchData['bank'] . ' - ' . $branchData['branch']); ?> | IFSC Finder</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet CSS for live map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --primary: #3b7cff;
            --secondary: #8b5cf6;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #1e293b;
            --light: #f8fafc;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        /* Header Styles */
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
        
        .main-container {
            background: rgba(255,255,255,0.95);
            border-radius: 30px;
            padding: 30px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            backdrop-filter: blur(10px);
        }
        
        .card-custom {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            background: white;
            padding: 25px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }
        
        .card-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }
        
        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark);
            border-left: 5px solid var(--primary);
            padding-left: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            color: #334155;
            padding: 8px;
            border-radius: 12px;
            transition: background 0.2s;
        }
        
        .detail-item:hover {
            background: var(--light);
        }
        
        .detail-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.2rem;
        }
        
        .badge-rating {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: var(--dark);
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 40px;
            font-size: 0.95rem;
        }
        
        .nearest-branch-item {
            padding: 15px;
            border-radius: 15px;
            background: var(--light);
            margin-bottom: 12px;
            transition: all 0.2s;
            border: 1px solid #e2e8f0;
        }
        
        .nearest-branch-item:hover {
            background: white;
            border-color: var(--primary);
            transform: translateX(5px);
        }
        
        .distance-badge {
            background: var(--primary);
            color: white;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .voice-btn {
            background: linear-gradient(135deg, var(--secondary), #7c3aed);
            color: white;
            border-radius: 60px;
            padding: 16px 32px;
            font-size: 1.2rem;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(139,92,246,0.3);
        }
        
        .voice-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(139,92,246,0.4);
        }
        
        .voice-btn.listening {
            background: linear-gradient(135deg, var(--success), #059669);
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16,185,129,0.7); }
            70% { box-shadow: 0 0 0 15px rgba(16,185,129,0); }
            100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
        }
        
        .facility-tag {
            background: #e2e8f0;
            color: var(--dark);
            padding: 6px 15px;
            border-radius: 30px;
            font-size: 0.9rem;
            display: inline-block;
            margin: 0 5px 5px 0;
            transition: all 0.2s;
        }
        
        .facility-tag:hover {
            background: var(--primary);
            color: white;
            transform: scale(1.05);
        }
        
        .share-btn {
            border: none;
            background: var(--light);
            color: var(--dark);
            padding: 12px 24px;
            border-radius: 40px;
            font-weight: 500;
            transition: all 0.2s;
            margin-right: 10px;
            margin-bottom: 10px;
            border: 1px solid #e2e8f0;
        }
        
        .share-btn:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .map-container {
            border-radius: 20px;
            overflow: hidden;
            border: 3px solid white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            height: 350px;
            width: 100%;
        }
        
        #liveMap {
            height: 100%;
            width: 100%;
            z-index: 1;
        }
        
        .state-district-selector {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            border-radius: 20px;
            color: white;
            margin-bottom: 25px;
        }
        
        .selector-dropdown {
            background: white;
            border: none;
            border-radius: 40px;
            padding: 12px 25px;
            font-weight: 500;
            color: var(--dark);
            width: 100%;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .selector-dropdown:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .weather-widget {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: white;
            border-radius: 20px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .download-stats {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .download-stats.withdraw {
            background: linear-gradient(135deg, #dc3545, #f43f5e);
        }
        
        .download-stats.transfer {
            background: linear-gradient(135deg, #007bff, #6610f2);
        }
        
        .download-stats .number {
            font-size: 24px;
            font-weight: 700;
        }
        
        .download-stats .label {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .download-btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border: none;
            border-radius: 40px;
            font-weight: 600;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
        }
        
        .download-btn.csv {
            background: #28a745;
            color: white;
        }
        
        .download-btn.pdf {
            background: #dc3545;
            color: white;
        }
        
        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .download-section-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--dark);
            margin: 15px 0 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid var(--primary);
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
    </style>
</head>
<body>

<!-- HEADER with 3-dot menu -->
<div class="header">
    <div class="three-dots" onclick="toggleMenu()">
        <i class="fa-solid fa-ellipsis-vertical"></i>
    </div>

    <div class="logo">
        <i class="fa-solid fa-building-columns"></i> BANK <span>IFSC Finder</span>
    </div>

    <!-- User Info Section - Only show if user is logged in -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="user-info">
        <div class="user-details">
            <i class="fas fa-user-circle"></i>
            <span><?php echo htmlspecialchars($full_name ?: $username); ?></span>
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
            <div class="user-email">Logged in</div>
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
    <div class="main-container">
        <!-- State/District Selector -->
        <div class="state-district-selector mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="text-white mb-2"><i class="fa-regular fa-compass me-2"></i> Find Branches by Location</h4>
                    <p class="text-white-50 mb-0">Select state and district to explore banks in your area</p>
                </div>
                <div class="col-md-4">
                    <select class="selector-dropdown" id="stateSelector" onchange="updateDistricts()">
                        <option value="">Select State</option>
                        <option value="delhi" <?php echo $branchData['state'] == 'Delhi' ? 'selected' : ''; ?>>Delhi</option>
                        <option value="maharashtra" <?php echo $branchData['state'] == 'Maharashtra' ? 'selected' : ''; ?>>Maharashtra</option>
                        <option value="karnataka" <?php echo $branchData['state'] == 'Karnataka' ? 'selected' : ''; ?>>Karnataka</option>
                        <option value="west bengal" <?php echo $branchData['state'] == 'West Bengal' ? 'selected' : ''; ?>>West Bengal</option>
                        <option value="tamil nadu" <?php echo $branchData['state'] == 'Tamil Nadu' ? 'selected' : ''; ?>>Tamil Nadu</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Header with Bank Info -->
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h1 class="display-6 fw-bold" style="color: var(--dark);">
                    <i class="fa-regular fa-building me-2" style="color: var(--primary);"></i>
                    <?php echo htmlspecialchars($branchData['bank']); ?>
                </h1>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary"><?php echo $branchData['category']; ?></span>
                    <span class="text-muted">Est. <?php echo $branchData['established']; ?></span>
                </div>
            </div>
            <div class="text-end">
                <span class="badge-rating">
                    <i class="fa-regular fa-star"></i> <?php echo $branchData['rating']; ?> (<?php echo $branchData['total_ratings']; ?> reviews)
                </span>
                <div class="mt-2">
                    <small class="text-muted">Branch Manager: <?php echo $branchData['manager']; ?></small>
                </div>
            </div>
        </div>

        <!-- Voice Assistant Button -->
        <div class="text-center mb-5">
            <button class="voice-btn" id="voiceAssistantBtn">
                <i class="fa-solid fa-headphones"></i> 🔊 Read Complete Branch Details
            </button>
            <p class="text-muted mt-2"><i class="fa-regular fa-circle-info"></i> Listen to all branch information including facilities and services</p>
        </div>

        <!-- Main Content Grid -->
        <div class="row g-4">
            <!-- Left Column - Main Branch Details -->
            <div class="col-lg-8">
                <!-- Branch Information Card -->
                <div class="card-custom" id="bankDetailsCard">
                    <h2 class="section-title">
                        <i class="fa-regular fa-circle-info"></i> Complete Branch Information
                    </h2>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-icon"><i class="fa-regular fa-building"></i></div>
                                <div><strong>Bank:</strong> <?php echo htmlspecialchars($branchData['bank']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon"><i class="fa-regular fa-map"></i></div>
                                <div><strong>Branch:</strong> <?php echo htmlspecialchars($branchData['branch']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon"><i class="fa-solid fa-qrcode"></i></div>
                                <div><strong>IFSC:</strong> <span class="font-monospace"><?php echo htmlspecialchars($branchData['ifsc']); ?></span></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon"><i class="fa-regular fa-credit-card"></i></div>
                                <div><strong>MICR:</strong> <?php echo htmlspecialchars($branchData['micr']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-icon"><i class="fa-regular fa-phone"></i></div>
                                <div><strong>Contact:</strong> <?php echo htmlspecialchars($branchData['contact']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon"><i class="fa-regular fa-envelope"></i></div>
                                <div><strong>Email:</strong> <?php echo htmlspecialchars($branchData['email']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon"><i class="fa-regular fa-calendar"></i></div>
                                <div><strong>Established:</strong> <?php echo $branchData['established']; ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon"><i class="fa-regular fa-location-dot"></i></div>
                                <div><strong>Pincode:</strong> <?php echo $branchData['pincode']; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fa-regular fa-address-card"></i></div>
                        <div><strong>Full Address:</strong> <?php echo htmlspecialchars($branchData['address']); ?>, <?php echo $branchData['city']; ?>, <?php echo $branchData['district']; ?>, <?php echo $branchData['state']; ?> - <?php echo $branchData['pincode']; ?></div>
                    </div>
                    
                    <!-- Facilities -->
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3"><i class="fa-regular fa-star me-2"></i> Branch Facilities</h6>
                        <div>
                            <?php foreach ($branchData['facilities'] as $facility): ?>
                                <span class="facility-tag"><i class="fa-regular fa-circle-check me-1"></i> <?php echo $facility; ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Services -->
                    <div class="mt-3">
                        <h6 class="fw-bold mb-3"><i class="fa-regular fa-gear me-2"></i> Banking Services</h6>
                        <div>
                            <?php foreach ($branchData['services'] as $service): ?>
                                <span class="facility-tag"><i class="fa-regular fa-bolt me-1"></i> <?php echo $service; ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Working Hours Card -->
                <div class="card-custom">
                    <h2 class="section-title">
                        <i class="fa-regular fa-clock"></i> Banking Hours & Holidays
                    </h2>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="working-hours-grid p-3 bg-light rounded-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fa-regular fa-sun me-2"></i>Monday – Friday</span>
                                    <span class="fw-bold"><?php echo $branchData['monday_friday']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fa-regular fa-calendar me-2"></i>Saturday</span>
                                    <span class="fw-bold"><?php echo $branchData['saturday']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="fa-regular fa-moon me-2"></i>Sunday</span>
                                    <span class="fw-bold text-danger"><?php echo $branchData['sunday']; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded-4">
                                <h6 class="fw-bold mb-2"><i class="fa-regular fa-gift me-2"></i> Holiday Schedule</h6>
                                <p class="mb-0"><?php echo $branchData['holidays']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Map Location -->
                <div class="card-custom">
                    <h2 class="section-title">
                        <i class="fa-regular fa-map"></i> Live Branch Location
                    </h2>
                    <div class="map-container">
                        <div id="liveMap"></div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $mapQuery; ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fa-regular fa-location-dot me-2"></i>View on Google Maps
                        </a>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $branchData['latitude']; ?>,<?php echo $branchData['longitude']; ?>" target="_blank" class="btn btn-outline-success rounded-pill px-4">
                            <i class="fa-regular fa-route me-2"></i>Get Directions
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column - Interactive Features + Download Options -->
            <div class="col-lg-4">
                <!-- Transaction Stats & Download Section -->
                <div class="card-custom">
                    <h2 class="section-title">
                        <i class="fa-regular fa-download"></i> Download Transaction History
                    </h2>
                    <p class="text-muted small mb-3">Export your transaction data from alldeatils.php</p>
                    
                    <!-- Deposit Stats & Download -->
                    <div class="download-stats">
                        <div class="number">₹<?php echo number_format($total_deposit); ?></div>
                        <div class="label">Total Deposits</div>
                    </div>
                    <div class="d-flex gap-2 mb-3">
                        <a href="?download=deposit&format=csv" class="download-btn csv flex-fill">
                            <i class="fa-regular fa-file-csv me-2"></i>CSV
                        </a>
                        <a href="?download=deposit&format=pdf" class="download-btn pdf flex-fill">
                            <i class="fa-regular fa-file-pdf me-2"></i>PDF
                        </a>
                    </div>
                    
                    <!-- Withdraw Stats & Download -->
                    <div class="download-stats withdraw">
                        <div class="number">₹<?php echo number_format($total_withdraw); ?></div>
                        <div class="label">Total Withdrawals</div>
                    </div>
                    <div class="d-flex gap-2 mb-3">
                        <a href="?download=withdraw&format=csv" class="download-btn csv flex-fill">
                            <i class="fa-regular fa-file-csv me-2"></i>CSV
                        </a>
                        <a href="?download=withdraw&format=pdf" class="download-btn pdf flex-fill">
                            <i class="fa-regular fa-file-pdf me-2"></i>PDF
                        </a>
                    </div>
                    
                    <!-- Transfer Stats & Download -->
                    <div class="download-stats transfer">
                        <div class="number"><?php echo $total_transfer; ?></div>
                        <div class="label">Total Transfers</div>
                    </div>
                    <div class="d-flex gap-2 mb-3">
                        <a href="?download=transfer&format=csv" class="download-btn csv flex-fill">
                            <i class="fa-regular fa-file-csv me-2"></i>CSV
                        </a>
                        <a href="?download=transfer&format=pdf" class="download-btn pdf flex-fill">
                            <i class="fa-regular fa-file-pdf me-2"></i>PDF
                        </a>
                    </div>
                    
                    <div class="alert alert-info mt-3 mb-0 py-2 small">
                        <i class="fa-regular fa-circle-info me-2"></i>
                        Downloads include last 1000 transactions from alldeatils.php
                    </div>
                </div>

                <!-- Nearest Branches -->
                <div class="card-custom">
                    <h2 class="section-title">
                        <i class="fa-regular fa-location-dot"></i> Nearest Branches
                        <span class="badge bg-success ms-2">Within 2 km</span>
                    </h2>
                    
                    <div class="nearest-branches-list">
                        <?php foreach ($nearestBranches as $nearby): ?>
                        <div class="nearest-branch-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0"><?php echo $nearby['bank']; ?></h6>
                                <span class="distance-badge"><i class="fa-regular fa-location-arrow me-1"></i> <?php echo $nearby['distance']; ?></span>
                            </div>
                            <p class="small text-muted mb-2"><?php echo $nearby['branch']; ?> Branch</p>
                            <p class="small mb-2"><i class="fa-regular fa-location-dot me-1"></i> <?php echo $nearby['address']; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small">
                                    <i class="fa-regular fa-star text-warning"></i> <?php echo $nearby['rating']; ?>/5
                                </span>
                                <a href="?ifsc=<?php echo $nearby['ifsc']; ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                    View Details
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Share Options -->
                <div class="card-custom">
                    <h2 class="section-title">
                        <i class="fa-regular fa-share-from-square"></i> Share This Branch
                    </h2>
                    <div class="d-grid gap-2">
                        <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($branchData['bank'] . ' ' . $branchData['branch'] . ' - IFSC: ' . $branchData['ifsc']); ?>" target="_blank" class="share-btn">
                            <i class="fa-brands fa-whatsapp me-2"></i>Share on WhatsApp
                        </a>
                        <a href="mailto:?subject=Bank Branch Details&body=<?php echo urlencode($branchData['bank'] . ' ' . $branchData['branch'] . "\n\nIFSC: " . $branchData['ifsc'] . "\nAddress: " . $branchData['address']); ?>" class="share-btn">
                            <i class="fa-regular fa-envelope me-2"></i>Share via Email
                        </a>
                        <button class="share-btn" onclick="copyPageLink()">
                            <i class="fa-regular fa-link me-2"></i>Copy Page Link
                        </button>
                    </div>
                    <div class="mt-2 text-center" id="copyMessage" style="display: none;">
                        <small class="text-success"><i class="fa-regular fa-circle-check"></i> Link copied!</small>
                    </div>
                </div>

                <!-- Weather Widget -->
                <div class="weather-widget">
                    <h6 class="text-white mb-3"><i class="fa-regular fa-cloud-sun me-2"></i> Local Weather</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="display-6 text-white">28°C</span>
                            <span class="text-white-50"> | Partly Cloudy</span>
                        </div>
                        <i class="fa-solid fa-cloud-sun fa-2x text-warning"></i>
                    </div>
                    <p class="text-white-50 small mt-2 mb-0"><i class="fa-regular fa-location-dot me-1"></i> <?php echo $branchData['city']; ?>, <?php echo $branchData['state']; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Live Clock -->
<div class="live-clock" id="live-clock">
    <i class="fas fa-clock"></i> Loading...
</div>

<!-- Footer Note -->
<div class="footer-note">
    <i class="fas fa-shield-alt"></i> Secure Banking Information
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Leaflet JS for live map -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Toggle menu function
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

    // Voice Assistant
    document.getElementById('voiceAssistantBtn').addEventListener('click', function() {
        const btn = this;
        const detailsCard = document.getElementById('bankDetailsCard');
        const details = detailsCard.innerText.replace(/\s+/g, ' ').trim();
        
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(details);
            utterance.lang = 'en-US';
            utterance.rate = 0.9;
            
            utterance.onstart = function() {
                btn.classList.add('listening');
                btn.innerHTML = '<i class="fa-solid fa-circle-pause"></i> 🔊 Reading Branch Details...';
            };
            
            utterance.onend = function() {
                btn.classList.remove('listening');
                btn.innerHTML = '<i class="fa-solid fa-headphones"></i> 🔊 Read Complete Branch Details';
            };
            
            window.speechSynthesis.speak(utterance);
        } else {
            alert('Your browser does not support voice synthesis.');
        }
    });

    // Copy link
    function copyPageLink() {
        navigator.clipboard.writeText(window.location.href).then(function() {
            const msg = document.getElementById('copyMessage');
            msg.style.display = 'block';
            setTimeout(() => { msg.style.display = 'none'; }, 2000);
        });
    }

    // State/District selector
    function updateDistricts() {
        const state = document.getElementById('stateSelector').value;
        alert('In production, districts would load for: ' + state);
    }

    // Initialize Live Map
    document.addEventListener('DOMContentLoaded', function() {
        const lat = <?php echo $branchData['latitude']; ?>;
        const lng = <?php echo $branchData['longitude']; ?>;
        
        // Create map
        const map = L.map('liveMap').setView([lat, lng], 15);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add marker for branch location
        const marker = L.marker([lat, lng]).addTo(map);
        marker.bindPopup(`
            <b><?php echo $branchData['bank']; ?></b><br>
            <?php echo $branchData['branch']; ?> Branch<br>
            <?php echo $branchData['address']; ?>
        `).openPopup();
        
        // Add circle to show area
        L.circle([lat, lng], {
            color: 'var(--primary)',
            fillColor: 'var(--primary)',
            fillOpacity: 0.1,
            radius: 200
        }).addTo(map);
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
        const currentPage = 'bankdetails.php';
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
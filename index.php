<?php
// ADD THIS AT THE VERY TOP - Login Protection
session_start();
require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login1.php");
    exit();
}

// Get logged-in user info
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];

// Get unread security alerts count
$alerts_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM security_alerts WHERE user_id = $user_id AND is_read = FALSE");
$alert_count = 0;
if ($alerts_query) {
    $alert_count = mysqli_fetch_assoc($alerts_query)['count'];
}

// Your existing code continues below - NOTHING CHANGED
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "db.php";

/* Stats - Keeping all your original stats */
$totalBanks = $totalAccounts = $todayDeposits = $todayWithdrawals = 0;

// Get total unique banks from branches table
$res = mysqli_query($conn,"SELECT COUNT(DISTINCT bank_name) total FROM branches"); 
if($res) $totalBanks = mysqli_fetch_assoc($res)['total'];

// Total branches count (6 banks × 36 districts = 216 branches)
$res = mysqli_query($conn,"SELECT COUNT(*) total FROM branches"); 
if($res) $totalBranches = mysqli_fetch_assoc($res)['total'] ?? 216;

// Total states count
$res = mysqli_query($conn,"SELECT COUNT(DISTINCT state) total FROM branches"); 
if($res) $totalStates = mysqli_fetch_assoc($res)['total'] ?? 6;

// Total districts count
$res = mysqli_query($conn,"SELECT COUNT(DISTINCT district) total FROM branches"); 
if($res) $totalDistricts = mysqli_fetch_assoc($res)['total'] ?? 36;

// For accounts - from your original accounts table
$res = mysqli_query($conn,"SELECT COUNT(*) total FROM account"); 
if($res) $totalAccounts = mysqli_fetch_assoc($res)['total'] ?? 3560;

// Transaction stats - KEEPING YOUR ORIGINAL DEPOSIT/WITHDRAWAL STATS
$res = mysqli_query($conn,"SELECT SUM(deposite) total FROM transactions WHERE DATE(date)=CURDATE()"); 
if($res) $todayDeposits = mysqli_fetch_assoc($res)['total'] ?? 1250000;

$res = mysqli_query($conn,"SELECT SUM(withdraw) total FROM transactions WHERE DATE(date)=CURDATE()"); 
if($res) $todayWithdrawals = mysqli_fetch_assoc($res)['total'] ?? 980000;

/* Recent searches */
$recentSearches = false;
$chk = mysqli_query($conn,"SHOW TABLES LIKE 'ifsc_searches'");
if($chk && mysqli_num_rows($chk) > 0){
    $recentSearches = mysqli_query($conn,"SELECT search_term,searched_at FROM ifsc_searches ORDER BY searched_at DESC LIMIT 5");
}

/* IFSC Search Result - Only Advanced Search */
$ifscResult = '';
$ifscDetails = [];
if(isset($_POST['search_ifsc'])){
    $bank = mysqli_real_escape_string($conn, $_POST['bank']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);

    // Build query based on what's selected
    $conditions = [];
    if(!empty($bank)) $conditions[] = "bank_name LIKE '%$bank%'";
    if(!empty($state)) $conditions[] = "state LIKE '%$state%'";
    if(!empty($district)) $conditions[] = "district LIKE '%$district%'";
    
    $where = implode(" AND ", $conditions);
    $query = mysqli_query($conn, "SELECT * FROM branches WHERE $where ORDER BY bank_name, state, district");
    
    if($query && mysqli_num_rows($query) > 0){
        while($row = mysqli_fetch_assoc($query)){
            $ifscDetails[] = $row;
        }
        // Log the search
        $search_term = "$bank - $state - $district";
        mysqli_query($conn, "INSERT INTO ifsc_searches(search_term, searched_at) VALUES('$search_term', NOW())");
    } else {
        $ifscResult = "No IFSC codes found for your search criteria!";
    }
}

// Calculate percentages for graphs - KEEPING ORIGINAL
$totalTransactions = $todayDeposits + $todayWithdrawals;
$depositPercent = $totalTransactions > 0 ? round(($todayDeposits / $totalTransactions) * 100) : 50;
$withdrawPercent = $totalTransactions > 0 ? round(($todayWithdrawals / $totalTransactions) * 100) : 50;

// Additional stats
$totalTransactionsToday = $todayDeposits + $todayWithdrawals;
$totalCustomers = $totalAccounts * 1.5;
$activeBanks = $totalBanks;
$successRate = 98;

// Get unique banks for suggestions
$banks_query = mysqli_query($conn, "SELECT DISTINCT bank_name FROM branches ORDER BY bank_name");
$banks_list = [];
while($row = mysqli_fetch_assoc($banks_query)) {
    $banks_list[] = $row['bank_name'];
}

// Get unique states
$states_query = mysqli_query($conn, "SELECT DISTINCT state FROM branches ORDER BY state");
$states_list = [];
while($row = mysqli_fetch_assoc($states_query)) {
    $states_list[] = $row['state'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BANK IFSC Finder - Home</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #F5F7FB;
    padding: 15px;
    height: 100vh;
    overflow: hidden;
}

/* Header with 3-dot menu - MODIFIED to fix clock overlap */
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

/* User Info Section - FIXED positioning */
.user-info {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
    justify-content: flex-end;
    max-width: 70%;
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

/* Dropdown Menu - UPDATED with user info in menu */
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

/* Rest of your existing styles - KEEP EVERYTHING EXACTLY AS IS */
.dashboard {
    max-width: 1400px;
    margin: 0 auto;
    height: calc(100vh - 100px);
    display: flex;
    flex-direction: column;
    gap: 10px;
    overflow-y: auto;
    padding-right: 5px;
}

.dashboard::-webkit-scrollbar {
    width: 5px;
}
.dashboard::-webkit-scrollbar-thumb {
    background: #2563EB;
    border-radius: 5px;
}

/* Welcome Text */
.welcome-text {
    margin-bottom: 10px;
    padding: 0 5px;
}
.welcome-text h2 {
    color: #2563EB;
    font-size: 20px;
    margin-bottom: 3px;
    font-weight: 600;
}
.welcome-text p {
    color: #64748B;
    font-size: 14px;
}

/* Stats Grid - KEEPING ORIGINAL WITH DEPOSIT/WITHDRAW */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 12px;
}
.stat-card {
    background: white;
    padding: 15px;
    border-radius: 12px;
    border: 1px solid #E9ECF2;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: #2563EB;
}
.stat-card.deposit::before { background: #059669; }
.stat-card.withdraw::before { background: #DC2626; }

.stat-label {
    color: #64748B;
    font-size: 13px;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.stat-value {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 5px;
}
.stat-value.blue { color: #2563EB; }
.stat-value.green { color: #059669; }
.stat-value.red { color: #DC2626; }

.stat-sub {
    font-size: 12px;
    color: #94A3B8;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Graph Container - KEEPING ORIGINAL */
.graph-container {
    width: 90px;
    height: 90px;
    margin: 8px auto 0;
    position: relative;
}
.deposit-graph, .withdraw-graph {
    width: 90px;
    height: 90px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    transition: transform 0.3s;
}
.deposit-graph:hover, .withdraw-graph:hover {
    transform: scale(1.05);
}
.deposit-graph {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    box-shadow: 0 6px 12px rgba(5, 150, 105, 0.3);
}
.withdraw-graph {
    background: linear-gradient(135deg, #DC2626 0%, #ef4444 100%);
    box-shadow: 0 6px 12px rgba(220, 38, 38, 0.3);
}
.graph-inner {
    text-align: center;
}
.graph-inner i {
    font-size: 28px;
    margin-bottom: 4px;
    display: block;
}
.graph-inner span {
    font-size: 16px;
}

/* Search Sections - Now only ONE search card */
.search-sections {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
    margin-bottom: 12px;
}

.search-card {
    background: white;
    padding: 18px;
    border-radius: 12px;
    border: 1px solid #E9ECF2;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.search-card h3 {
    font-size: 18px;
    color: #1E293B;
    margin-bottom: 15px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.search-card h3 i {
    color: #2563EB;
}

.search-input {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #E2E8F0;
    border-radius: 8px;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 500;
    background: #F8FAFC;
    transition: all 0.2s;
}
.search-input:focus {
    outline: none;
    border-color: #2563EB;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Search Button */
.search-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    background: #2563EB;
    color: white;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
    width: 100%;
}
.search-btn:hover {
    background: #1d4ed8;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
}
.search-btn i {
    margin-right: 8px;
}

/* IFSC Result Card */
.ifsc-result-card {
    margin-top: 15px;
    padding: 15px;
    background: #EFF6FF;
    border-radius: 10px;
    border-left: 4px solid #2563EB;
    animation: slideIn 0.3s;
}

.ifsc-result-card h4 {
    color: #2563EB;
    margin-bottom: 10px;
    font-size: 16px;
}

.ifsc-detail-item {
    display: grid;
    grid-template-columns: 100px 1fr;
    padding: 8px 0;
    border-bottom: 1px solid #E2E8F0;
}
.ifsc-detail-item:last-child {
    border-bottom: none;
}
.ifsc-label {
    color: #64748B;
    font-weight: 500;
}
.ifsc-value {
    color: #1E293B;
    font-weight: 600;
}

.ifsc-not-found {
    margin-top: 15px;
    padding: 15px;
    background: #FEE2E2;
    border-radius: 10px;
    border-left: 4px solid #DC2626;
    color: #DC2626;
    text-align: center;
}

/* Multiple Results */
.multiple-results {
    margin-top: 15px;
    max-height: 250px;
    overflow-y: auto;
}

.result-item {
    padding: 12px;
    background: #F8FAFC;
    border-radius: 8px;
    margin-bottom: 8px;
    border: 1px solid #E2E8F0;
    cursor: pointer;
    transition: all 0.2s;
}
.result-item:hover {
    background: #EFF6FF;
    border-color: #2563EB;
}
.result-item .ifsc-code {
    color: #2563EB;
    font-weight: 700;
    font-size: 16px;
}
.result-item .bank-details {
    color: #64748B;
    font-size: 13px;
    margin-top: 5px;
}

/* Main Grid */
.main-grid {
    display: grid;
    grid-template-columns: 1.4fr 0.6fr;
    gap: 12px;
    margin-bottom: 12px;
}

/* Left Column */
.left-column {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Right Column */
.right-column {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.recent-searches, .top-banks {
    background: white;
    padding: 18px;
    border-radius: 12px;
    border: 1px solid #E9ECF2;
    flex: 1;
    overflow-y: auto;
    max-height: 300px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.recent-searches h3, .top-banks h3 {
    font-size: 18px;
    color: #1E293B;
    margin-bottom: 15px;
    font-weight: 600;
}
.recent-searches h3 i, .top-banks h3 i {
    color: #2563EB;
    margin-right: 8px;
}
.bank-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #EDF2F7;
    font-size: 14px;
    font-weight: 500;
}
.bank-item:last-child {
    border-bottom: none;
}
.bank-info {
    display: flex;
    gap: 10px;
    align-items: center;
}
.bank-name {
    font-weight: 600;
    color: #1E293B;
    font-size: 14px;
}
.bank-codes {
    color: #059669;
    font-size: 14px;
    font-weight: 600;
    background: #E7F5E9;
    padding: 3px 8px;
    border-radius: 20px;
}

/* Help Section */
.help-section {
    background: white;
    padding: 15px;
    border-radius: 12px;
    border: 1px solid #E9ECF2;
    margin-bottom: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.help-section h3 {
    font-size: 20px;
    color: #1E293B;
    margin-bottom: 15px;
    font-weight: 600;
}
.help-section h3 i {
    color: #2563EB;
    margin-right: 8px;
}
.help-btn {
    padding: 10px 18px;
    border: 2px solid #E2E8F0;
    border-radius: 8px;
    background: #F8FAFC;
    cursor: pointer;
    margin-right: 10px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s;
}
.help-btn:hover {
    background: #2563EB;
    color: white;
    border-color: #2563EB;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(37, 99, 235, 0.2);
}
.help-btn i {
    margin-right: 6px;
}

/* Map Section */
.map-section {
    background: white;
    padding: 15px;
    border-radius: 12px;
    border: 1px solid #E9ECF2;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.map-section h4 {
    color: #64748B;
    font-size: 14px;
    text-transform: uppercase;
    margin-bottom: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
}
.map-placeholder {
    background: linear-gradient(135deg, #F1F5F9 0%, #E9ECF2 100%);
    height: 80px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1E293B;
    font-size: 14px;
    font-weight: 600;
    border: 2px dashed #2563EB;
    cursor: pointer;
    transition: all 0.3s;
}
.map-placeholder:hover {
    background: #E9ECF2;
    transform: scale(1.02);
    border-color: #059669;
}
.map-placeholder i {
    margin-right: 8px;
    color: #2563EB;
    font-size: 18px;
}

/* Quick Stats */
.quick-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 10px;
    background: white;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #E9ECF2;
}
.quick-stat-item {
    text-align: center;
}
.quick-stat-label {
    font-size: 11px;
    color: #64748B;
}
.quick-stat-value {
    font-size: 18px;
    font-weight: 700;
    color: #1E293B;
}

/* Features Row - KEEPING ORIGINAL */
.features-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 12px;
}
.feature-card {
    background: white;
    padding: 12px;
    border-radius: 12px;
    border: 1px solid #E9ECF2;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    transition: all 0.3s;
}
.feature-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    border-color: #2563EB;
}
.feature-icon {
    width: 45px;
    height: 45px;
    background: #EFF6FF;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2563EB;
    font-size: 20px;
}
.feature-text {
    font-size: 14px;
    color: #1E293B;
    font-weight: 600;
}
.feature-text small {
    color: #64748B;
    font-size: 11px;
    font-weight: 400;
    display: block;
    margin-top: 2px;
}
.feature-number {
    font-size: 24px;
    font-weight: 700;
    color: #2563EB;
    margin-left: auto;
}

/* Badge */
.badge {
    background: #2563EB;
    color: white;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 600;
    margin-left: 5px;
}

/* Live Clock - REPOSITIONED to bottom right */
.live-clock {
    position: fixed;
    bottom: 25px;
    right: 35px;
    background: white;
    padding: 8px 15px;
    border-radius: 30px;
    border: 1px solid #E9ECF2;
    font-weight: 600;
    color: #2563EB;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    z-index: 1000;
    font-size: 14px;
    backdrop-filter: blur(5px);
    border: 1px solid rgba(37, 99, 235, 0.2);
}

.live-clock:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(37, 99, 235, 0.2);
}

/* Bank Tags */
.bank-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 15px;
}
.bank-tag {
    background: #EFF6FF;
    color: #2563EB;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid #2563EB;
    transition: all 0.2s;
}
.bank-tag:hover {
    background: #2563EB;
    color: white;
}

/* Responsive design for smaller screens */
@media (max-width: 1200px) {
    .user-info {
        gap: 10px;
    }
    
    .user-details span {
        display: none;
    }
    
    .user-details {
        padding: 6px 10px;
    }
    
    .security-link span, .logout-btn span {
        display: none;
    }
    
    .security-link i, .logout-btn i {
        margin: 0;
    }
}

@media (max-width: 768px) {
    .logo {
        font-size: 18px;
    }
    
    .logo span {
        font-size: 20px;
    }
    
    .user-info {
        max-width: 60%;
    }
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
</head>

<body>

<!-- Header with 3-dot menu - MODIFIED to include user info -->
<div class="header">
    <div class="three-dots" onclick="toggleMenu()">
        <i class="fa-solid fa-ellipsis-vertical"></i>
    </div>

    <div class="logo">
        <i class="fa-solid fa-building-columns"></i> BANK <span>IFSC Finder</span>
    </div>

    <!-- User Info Section -->
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

    <!-- Dropdown Menu -->
    <div class="dropdown" id="menu">
        <div class="dropdown-user">
            <div class="user-name"><?php echo htmlspecialchars($full_name ?: $username); ?></div>
            <div class="user-email">Logged in</div>
        </div>
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
        <a href="logout1.php" style="background:#DC2626;"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="dashboard">
    <!-- Welcome Text - MODIFIED to show user name -->
    <div class="welcome-text">
        <h2>Welcome, <?php echo htmlspecialchars($full_name ?: $username); ?>! <span class="badge">Live</span></h2>
        <p>Search IFSC codes across 6 major banks and 36 districts</p>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats">
        <div class="quick-stat-item">
            <div class="quick-stat-label">Total Banks</div>
            <div class="quick-stat-value"><?php echo $totalBanks; ?></div>
        </div>
        <div class="quick-stat-item">
            <div class="quick-stat-label">Total Branches</div>
            <div class="quick-stat-value"><?php echo $totalBranches; ?></div>
        </div>
        <div class="quick-stat-item">
            <div class="quick-stat-label">States</div>
            <div class="quick-stat-value"><?php echo $totalStates; ?></div>
        </div>
        <div class="quick-stat-item">
            <div class="quick-stat-label">Districts</div>
            <div class="quick-stat-value"><?php echo $totalDistricts; ?></div>
        </div>
    </div>

    <!-- Stats with Graphs -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">🏦 Total Banks</div>
            <div class="stat-value blue"><?php echo number_format($totalBanks); ?></div>
            <div class="stat-sub"><i class="fas fa-arrow-up trend-up"></i> 6 Major Banks</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">👥 Total Accounts</div>
            <div class="stat-value blue"><?php echo number_format($totalAccounts); ?></div>
            <div class="stat-sub"><i class="fas fa-arrow-up trend-up"></i> +<?php echo number_format($totalAccounts/10); ?> new</div>
        </div>
        <div class="stat-card deposit">
            <div class="stat-label">💰 Today's Deposits</div>
            <div class="stat-value green">₹<?php echo number_format($todayDeposits); ?></div>
            <div class="stat-sub"><i class="fas fa-arrow-up trend-up"></i> +15.3%</div>
            <div class="graph-container">
                <div class="deposit-graph">
                    <div class="graph-inner">
                        <i class="fas fa-arrow-down"></i>
                        <span><?php echo $depositPercent; ?>%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="stat-card withdraw">
            <div class="stat-label">💳 Today's Withdrawals</div>
            <div class="stat-value red">₹<?php echo number_format($todayWithdrawals); ?></div>
            <div class="stat-sub"><i class="fas fa-arrow-down trend-down"></i> -5.2%</div>
            <div class="graph-container">
                <div class="withdraw-graph">
                    <div class="graph-inner">
                        <i class="fas fa-arrow-up"></i>
                        <span><?php echo $withdrawPercent; ?>%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Tags for Quick Selection -->
    <div class="bank-tags">
        <?php foreach($banks_list as $bank): ?>
        <span class="bank-tag" onclick="document.getElementById('bank').value='<?php echo $bank; ?>'; getDistrictsForBank();">
            <?php echo $bank; ?>
        </span>
        <?php endforeach; ?>
    </div>

    <!-- ONLY Advanced IFSC Search -->
    <div class="search-sections">
        <div class="search-card">
            <h3><i class="fas fa-filter"></i> Advanced IFSC Search ( Banks ×  States ×  Districts)</h3>
            <form method="post" id="searchForm" autocomplete="off">
                <!-- Bank Input with Datalist -->
                <input type="text" id="bank" name="bank" class="search-input" placeholder="🏦 Select Bank" list="bank-list" onchange="getDistrictsForBank()" required>
                <datalist id="bank-list">
                    <?php foreach($banks_list as $bank): ?>
                    <option value="<?php echo $bank; ?>">
                    <?php endforeach; ?>
                </datalist>

                <!-- State Input with Datalist -->
                <input type="text" id="state" name="state" class="search-input" placeholder="📍 Select State" list="state-list" onchange="getDistrictsForBank()" required>
                <datalist id="state-list">
                    <?php foreach($states_list as $state): ?>
                    <option value="<?php echo $state; ?>">
                    <?php endforeach; ?>
                </datalist>

                <!-- District Dropdown - Dynamically populated -->
                <select id="district" name="district" class="search-input" required>
                    <option value="">🏙️ Select District</option>
                </select>

                <button type="submit" name="search_ifsc" class="search-btn"><i class="fas fa-search"></i> Search IFSC Codes</button>
            </form>

            <?php if($ifscResult != ''): ?>
                <div class="ifsc-not-found">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $ifscResult; ?>
                </div>
            <?php endif; ?>

            <?php if(!empty($ifscDetails)): ?>
                <div class="ifsc-result-card">
                    <h4><i class="fas fa-check-circle" style="color:#059669;"></i> Found <?php echo count($ifscDetails); ?> Branch(es)</h4>
                    <div class="multiple-results">
                        <?php foreach($ifscDetails as $bank): ?>
                            <div class="result-item" onclick="copyIFSC('<?php echo $bank['ifsc_code']; ?>')">
                                <div class="ifsc-code"><?php echo $bank['ifsc_code']; ?></div>
                                <div class="bank-details">
                                    <strong><?php echo $bank['bank_name']; ?></strong> - <?php echo $bank['branch']; ?><br>
                                    <?php echo $bank['city']; ?>, <?php echo $bank['district']; ?>, <?php echo $bank['state']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="main-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Branch Locator -->
            <div class="search-card">
                <h3><i class="fas fa-map-marker-alt"></i> Branch Locator</h3>
                <button class="search-btn" onclick="alert('📍 Finding nearest branches...')">
                    <i class="fas fa-location-dot"></i> Find Nearest Branches
                </button>
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Recent IFSC Searches -->
            <div class="recent-searches">
                <h3><i class="fas fa-history"></i> Recent IFSC Searches</h3>
                <?php
                if($recentSearches && mysqli_num_rows($recentSearches)>0){
                    while($r=mysqli_fetch_assoc($recentSearches)){
                        echo "<div class='bank-item'>
                                <div class='bank-info'>
                                    <i class='fas fa-search' style='color:#2563EB;'></i>
                                    <span class='bank-name'>{$r['search_term']}</span>
                                </div>
                                <div style='color:#64748B;font-size:12px;'>{$r['searched_at']}</div>
                              </div>";
                    }
                } else { 
                    echo "<div style='color:#94A3B8;font-size:13px;text-align:center;padding:15px;'>
                            <i class='fas fa-inbox' style='font-size:24px;margin-bottom:8px;display:block;'></i>
                            No recent searches
                          </div>"; 
                }
                ?>
            </div>

            <!-- Top Banks -->
            <div class="top-banks">
                <h3><i class="fas fa-trophy"></i> Top Banks</h3>
                <div class="bank-item">
                    <div class="bank-info">
                        <span class="rank">🥇</span>
                        <span class="bank-name">State Bank of India</span>
                    </div>
                    <span class="bank-codes"><i class="fas fa-code"></i> 36 IFSC</span>
                </div>
                <div class="bank-item">
                    <div class="bank-info">
                        <span class="rank">🥈</span>
                        <span class="bank-name">Canara Bank</span>
                    </div>
                    <span class="bank-codes"><i class="fas fa-code"></i> 36 IFSC</span>
                </div>
                <div class="bank-item">
                    <div class="bank-info">
                        <span class="rank">🥉</span>
                        <span class="bank-name">Bank of Baroda</span>
                    </div>
                    <span class="bank-codes"><i class="fas fa-code"></i> 36 IFSC</span>
                </div>
                <div class="bank-item">
                    <div class="bank-info">
                        <span class="rank">4</span>
                        <span class="bank-name">Union Bank</span>
                    </div>
                    <span class="bank-codes"><i class="fas fa-code"></i> 36 IFSC</span>
                </div>
                <div class="bank-item">
                    <div class="bank-info">
                        <span class="rank">5</span>
                        <span class="bank-name">Punjab National Bank</span>
                    </div>
                    <span class="bank-codes"><i class="fas fa-code"></i> 36 IFSC</span>
                </div>
                <div class="bank-item">
                    <div class="bank-info">
                        <span class="rank">6</span>
                        <span class="bank-name">Bank of India</span>
                    </div>
                    <span class="bank-codes"><i class="fas fa-code"></i> 36 IFSC</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Section -->
    <div class="help-section">
        <h3><i class="fas fa-question-circle"></i> How Can I Assist You Today?</h3>
        <button class="help-btn" onclick="alert('🔍 Use the Advanced Search above')"><i class="fas fa-search"></i> Find IFSC Code</button>
        <button class="help-btn" onclick="alert('📍 Nearest Branch feature')"><i class="fas fa-map-pin"></i> Nearest Branch</button>
        <button class="help-btn" onclick="alert('💰 Check Balance feature')"><i class="fas fa-wallet"></i> Check Balance</button>
        <button class="help-btn" onclick="alert('📊 View Reports')"><i class="fas fa-chart-bar"></i> View Reports</button>
    </div>

    <!-- Map Section -->
    <div class="map-section">
        <h4><i class="fas fa-map"></i> Branch Map</h4>
        <div class="map-placeholder" onclick="alert('🗺️ Opening Google Maps...')">
            <i class="fas fa-map-marked-alt"></i> Click to view Google Map
        </div>
    </div>
</div>

<!-- Live Clock - Now at BOTTOM RIGHT -->
<div class="live-clock" id="live-clock">
    <i class="fas fa-clock"></i> Loading...
</div>

<!-- JavaScript -->
<script>
// District mapping based on bank and state selection
const districtData = {
    'Karnataka': ['Bangalore', 'Mysuru', 'Mandya', 'Tumkur', 'Mangalore', 'Udupi'],
    'Tamil Nadu': ['Chennai', 'Coimbatore', 'Madurai', 'Salem', 'Tiruchirappalli', 'Erode'],
    'Maharashtra': ['Mumbai', 'Pune', 'Nagpur', 'Nashik', 'Aurangabad', 'Thane'],
    'Kerala': ['Kochi', 'Thiruvananthapuram', 'Kozhikode', 'Thrissur', 'Alappuzha', 'Kollam'],
    'Gujarat': ['Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Bhavnagar', 'Jamnagar'],
    'Rajasthan': ['Jaipur', 'Udaipur', 'Jodhpur', 'Ajmer', 'Bikaner', 'Alwar']
};

function toggleMenu(){
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

function getDistrictsForBank() {
    const state = document.getElementById('state').value;
    const districtSelect = document.getElementById('district');
    
    // Clear existing options
    districtSelect.innerHTML = '<option value="">🏙️ Select District</option>';
    
    if (state && districtData[state]) {
        districtData[state].forEach(district => {
            const option = document.createElement('option');
            option.value = district;
            option.textContent = district;
            districtSelect.appendChild(option);
        });
    }
}

// Copy IFSC code to clipboard
function copyIFSC(ifsc) {
    navigator.clipboard.writeText(ifsc).then(function() {
        alert('IFSC Code copied: ' + ifsc);
    }, function() {
        alert('Failed to copy IFSC code');
    });
}

// Live clock
function updateClock() {
    const now = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    document.getElementById('live-clock').innerHTML = '<i class="fas fa-clock"></i> ' + time;
}
setInterval(updateClock, 1000);

// Initialize districts if state is pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const stateInput = document.getElementById('state');
    if (stateInput.value) {
        getDistrictsForBank();
    }
});
</script>

</body>
</html>
<?php
session_start();
?>
<!-- HEADER -->
<div class="header">
    <div class="logo">Bank IFSC</div>

    <!-- Hamburger Icon / 3-dot menu -->
    <div class="hamburger" id="hamburger">
        <div></div>
        <div></div>
        <div></div>
    </div>
</div>

<!-- SIDEBAR MENU -->
<div class="sidebar" id="sidebar">
    <h2>Menu</h2>
    <a href="index.php">🏠 Home</a>
    <a href="about.php">ℹ️ About</a>
    <a href="adminlogin.php">👤 Admin Login</a>
    <a href="banklogin.php">🏦 Bank Login</a>
    <a href="customerlogin.php">👥 Customer Login</a>
    <a href="bankdetails.php">🏦 Bank Details</a>
    <a href="security.php">🛡️ Security</a>
    <a href="contact.php">📞 Contact</a>
    <a href="settings.php">⚙️ Settings</a>
</div>

<!-- HEADER + SIDEBAR SCRIPT -->
<script>
const hamburger = document.getElementById("hamburger");
const sidebar = document.getElementById("sidebar");

hamburger.addEventListener("click", () => {
    sidebar.classList.add("active");

    // Auto close after 5 seconds
    setTimeout(() => {
        sidebar.classList.remove("active");
    }, 5000);
});
</script>

<style>
/* --- HEADER & SIDEBAR CSS --- */
.header {
    background: #0b2b5b;
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.header .logo {
    font-size: 20px;
    font-weight: 700;
}

.hamburger {
    cursor: pointer;
    width: 40px;
    height: 30px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.hamburger div {
    height: 4px;
    background: white;
    border-radius: 5px;
}

.sidebar {
    position: fixed;
    left: -260px;
    top: 0;
    width: 260px;
    height: 100%;
    background: #0b2b5b;
    padding: 20px;
    transition: 0.5s;
    box-shadow: 0 0 15px rgba(0,0,0,0.4);
    z-index: 1000;
}

.sidebar.active {
    left: 0;
}

.sidebar h2 {
    color: white;
    margin-bottom: 30px;
}

.sidebar a {
    display: block;
    color: white;
    text-decoration: none;
    margin: 12px 0;
    padding: 10px;
    border-radius: 8px;
    background: rgba(255,255,255,0.08);
}

.sidebar a:hover {
    background: rgba(255,255,255,0.15);
}
</style>
<?php
session_start();
unset($_SESSION['admin']);
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Logout | Bank IFSC Finder</title>
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
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    position: relative;
    overflow: hidden;
}

/* Animated Background Pattern */
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

/* Floating Particles */
.particles {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
}

.particle {
    position: absolute;
    width: 6px;
    height: 6px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    pointer-events: none;
    animation: float 8s infinite ease-in-out;
}

@keyframes float {
    0%, 100% { transform: translateY(0) translateX(0); opacity: 0.3; }
    25% { transform: translateY(-20px) translateX(10px); opacity: 0.6; }
    50% { transform: translateY(-40px) translateX(-10px); opacity: 0.3; }
    75% { transform: translateY(-20px) translateX(20px); opacity: 0.6; }
}

/* Logout Card */
.logout-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 40px;
    padding: 60px 50px;
    box-shadow: 0 30px 70px -20px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.3);
    text-align: center;
    max-width: 500px;
    width: 100%;
    position: relative;
    z-index: 10;
    animation: slideUpFade 0.8s cubic-bezier(0.23, 1, 0.32, 1);
}

@keyframes slideUpFade {
    from {
        opacity: 0;
        transform: translateY(40px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Success Icon */
.icon-wrapper {
    margin-bottom: 30px;
    position: relative;
}

.icon-circle {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
    animation: pulse 2s infinite;
    box-shadow: 0 15px 35px rgba(16, 185, 129, 0.3);
}

.icon-circle i {
    font-size: 60px;
    color: white;
    animation: checkmark 0.6s ease-in-out;
}

@keyframes checkmark {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

@keyframes pulse {
    0%, 100% { box-shadow: 0 15px 35px rgba(16, 185, 129, 0.3); }
    50% { box-shadow: 0 25px 45px rgba(16, 185, 129, 0.5); }
}

/* Text Content */
.logout-card h1 {
    color: #1E293B;
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 15px;
    background: linear-gradient(135deg, #1E293B 0%, #2563EB 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.logout-card p {
    color: #64748B;
    font-size: 18px;
    margin-bottom: 30px;
    line-height: 1.6;
}

/* Redirect Timer */
.timer-container {
    background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
    padding: 20px;
    border-radius: 30px;
    margin: 30px 0;
    border: 1px solid #E2E8F0;
}

.timer-text {
    color: #64748B;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10px;
}

.timer-circle {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: white;
    font-size: 28px;
    font-weight: 700;
    border: 4px solid white;
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    animation: rotate 10s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.timer-progress {
    width: 100%;
    height: 6px;
    background: #E2E8F0;
    border-radius: 10px;
    margin-top: 15px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    width: 100%;
    animation: shrink 2s linear forwards;
}

@keyframes shrink {
    from { width: 100%; }
    to { width: 0%; }
}

/* Buttons */
.button-group {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
}

.btn {
    padding: 14px 30px;
    border: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-decoration: none;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    flex: 1;
}

.btn-primary:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 15px 30px rgba(102, 126, 234, 0.5);
}

.btn-secondary {
    background: white;
    color: #1E293B;
    border: 2px solid #E2E8F0;
    flex: 1;
}

.btn-secondary:hover {
    transform: translateY(-5px) scale(1.05);
    border-color: #667eea;
    background: #F8FAFC;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.btn i {
    font-size: 18px;
    transition: transform 0.3s;
}

.btn:hover i {
    transform: translateX(3px);
}

.btn-secondary:hover i {
    transform: translateX(-3px);
}

/* Quick Links */
.quick-links {
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px dashed #E2E8F0;
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.quick-link {
    color: #64748B;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s;
    padding: 5px 10px;
    border-radius: 30px;
}

.quick-link:hover {
    color: #667eea;
    background: #F8FAFC;
    transform: translateY(-2px);
}

.quick-link i {
    font-size: 12px;
}

/* Decorative Elements */
.decor-circle {
    position: absolute;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
    filter: blur(50px);
    z-index: 0;
}

.decor-1 {
    top: -100px;
    right: -100px;
    animation: moveBlob 20s infinite alternate;
}

.decor-2 {
    bottom: -100px;
    left: -100px;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.2));
    animation: moveBlob 15s infinite alternate-reverse;
}

@keyframes moveBlob {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(50px, 50px) scale(1.2); }
}

/* Responsive */
@media (max-width: 768px) {
    .logout-card {
        padding: 40px 25px;
    }
    
    .logout-card h1 {
        font-size: 28px;
    }
    
    .logout-card p {
        font-size: 16px;
    }
    
    .icon-circle {
        width: 100px;
        height: 100px;
    }
    
    .icon-circle i {
        font-size: 48px;
    }
    
    .button-group {
        flex-direction: column;
    }
    
    .timer-circle {
        width: 60px;
        height: 60px;
        font-size: 24px;
    }
    
    .quick-links {
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
}
</style>
</head>
<body>

<!-- Background Pattern -->
<div class="background-pattern"></div>

<!-- Decorative Blobs -->
<div class="decor-circle decor-1"></div>
<div class="decor-circle decor-2"></div>

<!-- Floating Particles -->
<div class="particles" id="particles"></div>

<!-- Logout Card -->
<div class="logout-card">
    
    <!-- Success Icon -->
    <div class="icon-wrapper">
        <div class="icon-circle">
            <i class="fas fa-check"></i>
        </div>
    </div>
    
    <!-- Main Content -->
    <h1>Successfully Logged Out!</h1>
    <p>Thank you for using Bank IFSC Finder. Your session has been securely terminated.</p>
    
    <!-- Timer Container -->
    <div class="timer-container">
        <div class="timer-text">Redirecting in</div>
        <div class="timer-circle" id="timer">2</div>
        <div class="timer-progress">
            <div class="progress-bar" id="progressBar"></div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="button-group">
        <a href="adminlogin.php" class="btn btn-primary">
            <i class="fas fa-sign-in-alt"></i> Login Again
        </a>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-home"></i> Home
        </a>
    </div>
    
    <!-- Quick Links -->
    <div class="quick-links">
        <a href="about.php" class="quick-link">
            <i class="fas fa-info-circle"></i> About
        </a>
        <a href="contact.php" class="quick-link">
            <i class="fas fa-phone"></i> Contact
        </a>
        <a href="help.php" class="quick-link">
            <i class="fas fa-question-circle"></i> Help
        </a>
    </div>
</div>

<script>
// Particles Animation
function createParticles() {
    const particlesContainer = document.getElementById('particles');
    const particleCount = 30;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        // Random positioning
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        
        // Random size
        const size = Math.random() * 4 + 2;
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';
        
        // Random animation delay
        particle.style.animationDelay = Math.random() * 5 + 's';
        
        // Random animation duration
        particle.style.animationDuration = (Math.random() * 5 + 5) + 's';
        
        particlesContainer.appendChild(particle);
    }
}

// Countdown Timer
let seconds = 2;
const timerElement = document.getElementById('timer');
const progressBar = document.getElementById('progressBar');

function updateTimer() {
    timerElement.textContent = seconds;
    
    if (seconds === 0) {
        window.location.href = "adminlogin.php";
    } else {
        seconds--;
        setTimeout(updateTimer, 1000);
    }
}

// Start countdown
setTimeout(updateTimer, 1000);

// Initialize particles
createParticles();

// Add click effect to buttons
document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', function(e) {
        let ripple = document.createElement('span');
        ripple.style.position = 'absolute';
        ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.3)';
        ripple.style.width = '10px';
        ripple.style.height = '10px';
        ripple.style.borderRadius = '50%';
        ripple.style.transform = 'translate(-50%, -50%)';
        ripple.style.animation = 'pulse 1s';
        ripple.style.pointerEvents = 'none';
        
        let x = e.clientX - e.target.offsetLeft;
        let y = e.clientY - e.target.offsetTop;
        
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        
        this.style.position = 'relative';
        this.style.overflow = 'hidden';
        this.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 1000);
    });
});

// Prevent multiple redirects if user clicks back
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});
</script>

</body>
</html>
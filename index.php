<?php

require_once "config/security.php";
require_once "config/database.php";

$isLoggedIn = isset($_SESSION["user_id"]);
$userName = $_SESSION["user_name"] ?? "";
$userRole = $_SESSION["role"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberRisk - Security Assessment Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container" style="max-width: 960px;">
    <div style="text-align: center; margin-bottom: 25px;">
        <div style="font-size: 58px; margin-bottom: 10px;">🛡️</div>
        <h1>CyberRisk Platform</h1>
        <p class="section-desc" style="font-size: 17px; max-width: 650px; margin: 0 auto 20px auto;">
            Comprehensive cybersecurity awareness and personal risk assessment engine. Evaluate your digital habits across 6 critical security dimensions.
        </p>
        <div class="status-row" style="justify-content: center; display: flex; gap: 12px; margin-bottom: 25px;">
            <span class="status-badge">⚡ System Online</span>
            <span class="status-badge">🗄️ MySQL Connected</span>
            <span class="status-badge">🔒 24 Security Audits</span>
        </div>
    </div>

    <div class="status-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <div class="card" style="margin: 0; padding: 20px;">
            <h3>🔑 Passwords & MFA</h3>
            <p class="section-desc" style="font-size: 14px; margin-top: 8px;">Evaluate password complexity, credential reuse, and multi-factor defense.</p>
        </div>
        <div class="card" style="margin: 0; padding: 20px;">
            <h3>📱 Device Hygiene</h3>
            <p class="section-desc" style="font-size: 14px; margin-top: 8px;">Assess OS updates, screen locking, malware protection, and physical security.</p>
        </div>
        <div class="card" style="margin: 0; padding: 20px;">
            <h3>🌐 Digital Privacy</h3>
            <p class="section-desc" style="font-size: 14px; margin-top: 8px;">Audit public exposure, tracking cookies, and sensitive data leakage.</p>
        </div>
        <div class="card" style="margin: 0; padding: 20px;">
            <h3>📧 Phishing Detection</h3>
            <p class="section-desc" style="font-size: 14px; margin-top: 8px;">Test resilience against email lures, malicious attachments, and homograph spoofs.</p>
        </div>
        <div class="card" style="margin: 0; padding: 20px;">
            <h3>📶 Network Security</h3>
            <p class="section-desc" style="font-size: 14px; margin-top: 8px;">Analyze public Wi-Fi risks, router encryption, VPN usage, and open ports.</p>
        </div>
        <div class="card" style="margin: 0; padding: 20px;">
            <h3>👥 Social Engineering</h3>
            <p class="section-desc" style="font-size: 14px; margin-top: 8px;">Measure vigilance against urgency scams, shoulder surfing, and pretexting.</p>
        </div>
    </div>

    <div class="card" style="text-align: center; padding: 30px; background: rgba(30, 41, 59, 0.95);">
        <?php if ($isLoggedIn): ?>
            <h2>Welcome Back, <?= htmlspecialchars($userName) ?>!</h2>
            <p class="section-desc">You are logged in as <strong><?= htmlspecialchars(ucfirst($userRole)) ?></strong>.</p>
            <div style="display: flex; gap: 15px; justify-content: center; max-width: 450px; margin: 20px auto 0 auto;">
                <button onclick="location.href='dashboard.php'">🚀 Enter Dashboard</button>
                <button onclick="location.href='logout.php'" style="background: #475569;">🚪 Logout</button>
            </div>
        <?php else: ?>
            <h2>Get Started with Your Assessment</h2>
            <p class="section-desc">Log in with an existing account or create a new student account to begin your security scan.</p>
            <div style="display: flex; gap: 15px; justify-content: center; max-width: 500px; margin: 20px auto 0 auto;">
                <button onclick="location.href='login.php'">🔐 Login</button>
                <button onclick="location.href='register.php'" style="background: #10b981;">📝 Register Account</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="card" style="margin-top: 25px; border-left: 4px solid #38bdf8;">
        <h3 style="margin-bottom: 10px;">🧪 Pre-Configured Demo Accounts</h3>
        <p class="section-desc" style="font-size: 14px; margin-bottom: 12px;">The database is seeded with sample accounts ready for immediate testing:</p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px; font-size: 14px;">
            <div style="background: rgba(15, 23, 42, 0.6); padding: 12px 16px; border-radius: 8px;">
                <strong>🎓 Student Account:</strong><br>
                Email: <code style="color: #38bdf8;">student@cyberrisk.local</code><br>
                Password: <code style="color: #38bdf8;">StudentPassword#2026</code>
            </div>
            <div style="background: rgba(15, 23, 42, 0.6); padding: 12px 16px; border-radius: 8px;">
                <strong>🛡️ Faculty Admin Account:</strong><br>
                Email: <code style="color: #38bdf8;">admin@cyberrisk.local</code><br>
                Password: <code style="color: #38bdf8;">AdminPassword#2026</code>
            </div>
        </div>
    </div>
</div>

</body>
</html>
        
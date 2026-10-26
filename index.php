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
        <div class="home-mark" aria-hidden="true"><svg class="icon icon-large"><use href="assets/cyberrisk-icons.svg#shield"></use></svg></div>
        <h1>CyberRisk Platform</h1>
        <p class="section-desc" style="font-size: 17px; max-width: 650px; margin: 0 auto 20px auto;">
              Assess your security habits across six key areas.
        </p>
        <div class="status-row" style="justify-content: center; display: flex; gap: 12px; margin-bottom: 25px;">
            <span class="status-badge">System online</span>
              <span class="status-badge"><svg class="icon" aria-hidden="true"><use href="assets/cyberrisk-icons.svg#database"></use></svg> MySQL Connected</span>
              <span class="status-badge"><svg class="icon" aria-hidden="true"><use href="assets/cyberrisk-icons.svg#clipboard"></use></svg> 24 Security Audits</span>
        </div>
    </div>

    <div class="status-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <div class="card" style="margin: 0; padding: 20px;">
              <h3><svg class="icon" aria-hidden="true"><use href="assets/cyberrisk-icons.svg#key"></use></svg> Passwords & MFA</h3>
              <p class="section-desc" style="font-size: 14px; margin-top: 8px;">Check password strength, reuse, and multi-factor protection.</p>
        </div>
        <div class="card" style="margin: 0; padding: 20px;">
              <h3><svg class="icon" aria-hidden="true"><use href="assets/cyberrisk-icons.svg#device"></use></svg> Device Hygiene</h3>
              <p class="section-desc" style="font-size: 14px; margin-top: 8px;">Review updates, screen locks, and device protection.</p>
        </div>
        <div class="card" style="margin: 0; padding: 20px;">
              <h3><svg class="icon" aria-hidden="true"><use href="assets/cyberrisk-icons.svg#globe"></use></svg> Digital Privacy</h3>
              <p class="section-desc" style="font-size: 14px; margin-top: 8px;">Review your public profile and data sharing.</p>
        </div>
        <div class="card" style="margin: 0; padding: 20px;">
              <h3><svg class="icon" aria-hidden="true"><use href="assets/cyberrisk-icons.svg#mail"></use></svg> Phishing Detection</h3>
              <p class="section-desc" style="font-size: 14px; margin-top: 8px;">Practice spotting suspicious messages and links.</p>
        </div>
        <div class="card" style="margin: 0; padding: 20px;">
              <h3><svg class="icon" aria-hidden="true"><use href="assets/cyberrisk-icons.svg#wifi"></use></svg> Network Security</h3>
              <p class="section-desc" style="font-size: 14px; margin-top: 8px;">Review Wi-Fi, router, and VPN habits.</p>
        </div>
        <div class="card" style="margin: 0; padding: 20px;">
              <h3><svg class="icon" aria-hidden="true"><use href="assets/cyberrisk-icons.svg#users"></use></svg> Social Engineering</h3>
              <p class="section-desc" style="font-size: 14px; margin-top: 8px;">Review how you handle urgent or unusual requests.</p>
        </div>
    </div>

    <div class="card" style="text-align: center; padding: 30px; background: rgba(30, 41, 59, 0.95);">
        <?php if ($isLoggedIn): ?>
            <h2>Welcome Back, <?= htmlspecialchars($userName) ?>!</h2>
            <p class="section-desc">You are logged in as <strong><?= htmlspecialchars(ucfirst($userRole)) ?></strong>.</p>
            <div style="display: flex; gap: 15px; justify-content: center; max-width: 450px; margin: 20px auto 0 auto;">
                <button onclick="location.href='dashboard.php'">Dashboard</button>
                <button onclick="location.href='logout.php'" style="background: #475569;">Log out</button>
            </div>
        <?php else: ?>
              <h2>CyberRisk Assessment</h2>
              <p class="section-desc">Sign in or create an account to begin.</p>
            <div style="display: flex; gap: 15px; justify-content: center; max-width: 500px; margin: 20px auto 0 auto;">
                 <button onclick="location.href='login.php'">Log in</button>
                 <button onclick="location.href='register.php'" style="background: #10b981;">Create account</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="card" style="margin-top: 25px; border-left: 4px solid #38bdf8;">
           <h3 style="margin-bottom: 10px;">Demo Accounts</h3>
           <p class="section-desc" style="font-size: 14px; margin-bottom: 12px;">Use either account to try the app:</p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px; font-size: 14px;">
            <div style="background: rgba(15, 23, 42, 0.6); padding: 12px 16px; border-radius: 8px;">
                 <strong>Student:</strong><br>
                Email: <code style="color: #38bdf8;">student@cyberrisk.local</code><br>
                Password: <code style="color: #38bdf8;">StudentPassword#2026</code>
            </div>
            <div style="background: rgba(15, 23, 42, 0.6); padding: 12px 16px; border-radius: 8px;">
                 <strong>Faculty admin:</strong><br>
                Email: <code style="color: #38bdf8;">admin@cyberrisk.local</code><br>
                Password: <code style="color: #38bdf8;">AdminPassword#2026</code>
            </div>
        </div>
    </div>
</div>

</body>
</html>
        
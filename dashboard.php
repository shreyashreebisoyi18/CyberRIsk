<?php

require_once "includes/auth.php";

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>CyberRisk Dashboard</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="container">

    <h1>
        Welcome, <?= htmlspecialchars($_SESSION["user_name"]) ?> 👋
    </h1>

    <div class="status-row">

        <span class="status-badge">🔐 Secure Session</span>

        <span class="status-badge">
            <?= htmlspecialchars(ucfirst($_SESSION["role"])) ?>
        </span>

        <span class="status-badge">
            <?= date("d M Y") ?>
        </span>

    </div>

    <div class="card">

        <h2>Your CyberRisk Dashboard</h2>

        <p>
            Welcome back! Your account is securely authenticated.
            Take a CyberRisk Assessment to analyse your cybersecurity habits
            across six key security categories.
        </p>

    </div>

    <div class="card">

        <h2>Quick Actions</h2>

        <button onclick="location.href='assessment.php'">
            📝 Take CyberRisk Assessment
        </button>

        <?php if ($_SESSION["role"] == "admin"): ?>

            <button onclick="location.href='admin_dashboard.php'">
                📊 Open Admin Dashboard
            </button>

        <?php endif; ?>

        <button onclick="location.href='logout.php'">
            🚪 Logout
        </button>

    </div>

</div>

</body>

</html>
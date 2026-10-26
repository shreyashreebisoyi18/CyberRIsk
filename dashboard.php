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
        Welcome, <?= htmlspecialchars($_SESSION["user_name"]) ?>
    </h1>

    <div class="status-row">

        <span class="status-badge">Secure session</span>

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
            Review your profile or take an assessment across six security areas.
        </p>

    </div>

    <div class="card">

        <h2>Quick Actions</h2>

        <button onclick="location.href='assessment.php'">
            Take assessment
        </button>

        <?php if ($_SESSION["role"] == "admin"): ?>

            <button onclick="location.href='admin_dashboard.php'">
                Admin dashboard
            </button>

        <?php endif; ?>

        <button onclick="location.href='logout.php'">
            Log out
        </button>

    </div>

</div>

</body>

</html>
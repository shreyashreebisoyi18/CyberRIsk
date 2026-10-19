<?php

require_once "config/security.php";
require_once "config/database.php";
require_once "includes/csrf.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !isset($_POST["csrf_token"]) ||
        !verify_csrf_token($_POST["csrf_token"])
    ) {
        http_response_code(403);
        exit("Invalid request.");
    }

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, name, password_hash, role
         FROM users
         WHERE email=?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password_hash"])) {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["role"] = $user["role"];

            $stmt->close();

            header("Location: loading.php");
            exit;
        }
    }

    $message = "Invalid email or password.";

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>CyberRisk Login</title>

<link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="container">

<h1>CyberRisk Login</h1>

<p class="section-desc" style="text-align:center;">
Securely access your CyberRisk account.
</p>

<form method="POST">

<input
type="hidden"
name="csrf_token"
value="<?= htmlspecialchars(csrf_token()) ?>">

<input
type="email"
name="email"
placeholder="Email Address"
required
autocomplete="email">

<input
type="password"
name="password"
placeholder="Password"
required
autocomplete="current-password">

<button type="submit">
🔐 Login Securely
</button>

</form>

<?php if (!empty($message)): ?>

<div class="card" style="margin-top:20px;border-left:4px solid #ef4444;">
<p style="color:#fca5a5;">
<?= htmlspecialchars($message) ?>
</p>
</div>

<?php endif; ?>

<a href="register.php">
Create a New Account
</a>

</div>

</body>

</html>
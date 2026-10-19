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

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "") {

        $message = "Please enter your name.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email.";

    } elseif (strlen($password) < 12) {

        $message = "Password must be at least 12 characters.";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email=?"
        );

        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $message = "Email already registered.";

        } else {

            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users
                (name,email,password_hash)
                VALUES (?,?,?)"
            );

            $stmt->bind_param(
                "sss",
                $name,
                $email,
                $passwordHash
            );

            if ($stmt->execute()) {

                $message = "Registration successful!";

            } else {

                $message = "Something went wrong.";
            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>CyberRisk - Register</title>

<link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="container">

<h1>Create Account</h1>

<form method="POST">

<input
type="hidden"
name="csrf_token"
value="<?= htmlspecialchars(csrf_token()) ?>"
>

<input
type="text"
name="name"
placeholder="Full Name"
required
>

<input
type="email"
name="email"
placeholder="Email"
required
>

<input
type="password"
name="password"
placeholder="Password (12+ characters)"
required
>

<button type="submit">
Register
</button>

</form>

<p><?= htmlspecialchars($message) ?></p>

<a href="login.php">
Already have an account?
</a>

</div>

</body>

</html>
<?php

declare(strict_types=1);

$host = "localhost";
$db   = "cyberrisk";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    exit("A server error occurred.");
}

$conn->set_charset("utf8mb4");
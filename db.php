<?php
// ── Database Configuration ───────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Change to your MySQL username
define('DB_PASS', '');           // Change to your MySQL password
define('DB_NAME', 'library_db');

// ── Connect ──────────────────────────────────────────────────────────────────
function getConnection(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}

<?php
require_once __DIR__ . '/../app/db.php';

$username = 'admin';                // desired username
$plainPassword = 'SuperSecure123';  // desired password

$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
$stmt->execute([$username, $hashedPassword]);

echo "Admin user created successfully.";
<?php
require_once __DIR__ . '/../app/db.php';

try {
    // Test 1: check admins table
    $stmt = $pdo->query("SELECT COUNT(*) AS admin_count FROM admins");
    $adminRow = $stmt->fetch(PDO::FETCH_ASSOC);

    // Test 2: check admin_links table
    $stmt = $pdo->query("SELECT COUNT(*) AS link_count FROM admin_links");
    $linkRow = $stmt->fetch(PDO::FETCH_ASSOC);

    // Test 3: check invited_users table
    $stmt = $pdo->query("SELECT COUNT(*) AS invited_count FROM invited_users");
    $invitedRow = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "DB connected successfully!<br>";
    echo "Admins: " . $adminRow['admin_count'] . "<br>";
    echo "Invite Links: " . $linkRow['link_count'] . "<br>";
    echo "Invited Users: " . $invitedRow['invited_count'] . "<br>";

} catch (Exception $e) {
    echo "DB test failed: " . $e->getMessage();
}
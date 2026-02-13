<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../app/db.php';

// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="registered_users.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Invite Link', 'First Name', 'Last Name', 'Mainguest First', 'Mainguest Last', 'Attendance Days'], ';', '"', "\\");

    $stmt = $pdo->query("SELECT * FROM invited_users ORDER BY id ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Map attendance numeric to label
        $days = match ((int)$row['attendance_days']) {
            1 => 'Fr',
            2 => 'Sa',
            3 => 'Beide',
            default => ''
        };

        fputcsv($output, [
            (string)$row['invite_link'],
            (string)$row['firstname'],
            (string)$row['lastname'],
            (string)$row['firstname_mainguest'],
            (string)$row['lastname_mainguest'],
            $days
        ], ';', '"', "\\");
    }
    fclose($output);
    exit;
}

// Fetch users for display
$stmt = $pdo->query("SELECT * FROM invited_users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Map attendance numeric to label for display
function attendanceLabel($num) {
    return match ((int)$num) {
        1 => 'Fr',
        2 => 'Sa',
        3 => 'Beide',
        default => ''
    };
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registered Users</title>
</head>
<body>
    <h1>Registered Users</h1>
    <p><a href="dashboard.php">Back to Dashboard</a> | <a href="?export=csv">Export CSV</a></p>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Invite Link</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Mainguest First</th>
                <th>Mainguest Last</th>
                <th>Attendance Days</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars((string)$user['invite_link']); ?></td>
                    <td><?php echo htmlspecialchars((string)$user['firstname']); ?></td>
                    <td><?php echo htmlspecialchars((string)$user['lastname']); ?></td>
                    <td><?php echo htmlspecialchars((string)$user['firstname_mainguest']); ?></td>
                    <td><?php echo htmlspecialchars((string)$user['lastname_mainguest']); ?></td>
                    <td><?php echo attendanceLabel((string)$user['attendance_days']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
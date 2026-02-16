<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/services/CsvService.php';

$csvService = new CsvService($pdo);

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $csvService->exportRegisteredUsers();
}

// Daten für Tabelle laden
$users = $csvService->getAllUsers();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registrierte Personen</title>
</head>
<body>

<h1>Registrierte Personen</h1>

<p>
    <a href="dashboard.php">Zurück zum Admin-Bereich</a> |
    <a href="?export=csv">CSV exportieren</a>
</p>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Einladungslink</th>
            <th>Vorname</th>
            <th>Nachname</th>
            <th>Vorname Hauptgast</th>
            <th>Nachname Hauptgast</th>
            <th>Anwesenheit</th>
        </tr>
    </thead>
    <tbody>

    <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars((string)($user['invite_link'] ?? '')); ?></td>
            <td><?php echo htmlspecialchars((string)($user['firstname'] ?? '')); ?></td>
            <td><?php echo htmlspecialchars((string)($user['lastname'] ?? '')); ?></td>
            <td><?php echo htmlspecialchars((string)($user['firstname_mainguest'] ?? '')); ?></td>
            <td><?php echo htmlspecialchars((string)($user['lastname_mainguest'] ?? '')); ?></td>
            <td>
                <?php echo htmlspecialchars($csvService->attendanceLabel($user['attendance_days'] ?? '')); ?>
            </td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>

</body>
</html>
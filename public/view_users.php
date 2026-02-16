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

<?php include '../templates/header.php'; ?>

<div class="container my-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="display-4 fw-bold text-dark">Registrierte Personen</h1>
            <p class="lead text-muted">Übersicht aller Anmeldungen</p>
        </div>
        <div class="col-md-4 text-md-end">
             <a href="?export=csv" class="btn btn-success btn-lg shadow-sm">
                 <span class="me-2">CSV Exportieren</span>
            </a>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-lg ms-2">Zurück</a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4">Einladungslink</th>
                            <th class="py-3 px-4">Vorname</th>
                            <th class="py-3 px-4">Nachname</th>
                            <th class="py-3 px-4">Vorname Hauptgast</th>
                            <th class="py-3 px-4">Nachname Hauptgast</th>
                            <th class="py-3 px-4">Anwesenheit</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Keine Registrierungen gefunden.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="px-4 py-3 font-monospace small"><?php echo htmlspecialchars($user['invite_link'] ?? '-'); ?></td>
                                <td class="px-4 py-3 fw-bold"><?php echo htmlspecialchars($user['firstname'] ?? ''); ?></td>
                                <td class="px-4 py-3 fw-bold"><?php echo htmlspecialchars($user['lastname'] ?? ''); ?></td>
                                <td class="px-4 py-3 text-muted"><?php echo htmlspecialchars($user['firstname_mainguest'] ?? '-'); ?></td>
                                <td class="px-4 py-3 text-muted"><?php echo htmlspecialchars($user['lastname_mainguest'] ?? '-'); ?></td>
                                <td class="px-4 py-3">
                                    <span class="badge rounded-pill bg-primary">
                                        <?php echo htmlspecialchars($csvService->attendanceLabel($user['attendance_days'] ?? '')); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
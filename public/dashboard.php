<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/services/DashboardService.php';

$service = new DashboardService($pdo);

$admin = $service->getAdminById($_SESSION['admin_id']);

$generatedHauptgast = [];
$generatedPlusOne = [];
$whitelistMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Einladung generieren
    if (isset($_POST['generate_invite'])) {

        $numHauptgast = (int) ($_POST['num_hauptgast'] ?? 0);
        $numPlusOne   = (int) ($_POST['num_plusone'] ?? 0);

        if ($numHauptgast > 0) {
            $generatedHauptgast = $service->generateInviteLinks(
                $_SESSION['admin_id'],
                'Hauptgast',
                $numHauptgast
            );
        }

        if ($numPlusOne > 0) {
            $generatedPlusOne = $service->generateInviteLinks(
                $_SESSION['admin_id'],
                '+1 Link',
                $numPlusOne
            );
        }
    }

    // Whitelist hinzufügen
    if (isset($_POST['add_whitelist'])) {

        $wl_firstname = trim($_POST['wl_firstname'] ?? '');
        $wl_lastname  = trim($_POST['wl_lastname'] ?? '');

        $whitelistMessage = $service->addToWhitelist(
            $wl_firstname,
            $wl_lastname,
            $admin['username']
        );
    }
}
?>


<?php include '../templates/header.php'; ?>

<div class="container my-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="display-4 fw-bold text-dark">Dashboard</h1>
            <p class="lead text-muted">Willkommen zurück, <?php echo htmlspecialchars($admin['username'] ?? ''); ?>!</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="view_users.php" class="btn btn-alive btn-lg shadow-sm">
                 Alle Registrierungen ansehen
            </a>
            <a href="logout.php" class="btn btn-outline-secondary btn-lg ms-4">Abmelden</a>
        </div>
    </div>

    <hr class="mb-5">

    <div class="row g-4">
        <!-- Generate Links Column -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h3 class="card-title fw-bold text-primary">
                        Einladungslinks
                    </h3>
                </div>
                <div class="card-body px-4 pb-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Anzahl Hauptgast-Links</label>
                            <input type="number" class="form-control form-control-lg" name="num_hauptgast" min="0" value="0">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Anzahl +1-Links</label>
                            <input type="number" class="form-control form-control-lg" name="num_plusone" min="0" value="0">
                        </div>
                        <button type="submit" name="generate_invite" class="btn btn-primary w-100 py-2 fw-bold">
                            Links generieren
                        </button>
                    </form>


                    <?php if (!empty($generatedHauptgast) || !empty($generatedPlusOne)): ?>
                        <div class="mt-4 p-3 bg-light rounded">
                            <form action="export_word.php" method="POST" target="_blank">
                                <?php if (!empty($generatedHauptgast)): ?>
                                    <h6 class="fw-bold mb-2">Neue Hauptgast-Links:</h6>
                                    <ul class="list-unstyled mb-3">
                                        <?php foreach ($generatedHauptgast as $link): ?>
                                            <li class="mb-2">
                                                <input type="text" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($link); ?>" readonly onclick="this.select()">
                                                <input type="hidden" name="hauptgast_links[]" value="<?php echo htmlspecialchars($link); ?>">
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <?php if (!empty($generatedPlusOne)): ?>
                                    <h6 class="fw-bold mb-2">Neue +1-Links:</h6>
                                    <ul class="list-unstyled mb-0">
                                        <?php foreach ($generatedPlusOne as $link): ?>
                                            <li class="mb-2">
                                                <input type="text" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($link); ?>" readonly onclick="this.select()">
                                                <input type="hidden" name="plusone_links[]" value="<?php echo htmlspecialchars($link); ?>">
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        Als Word-Datei exportieren (mit QR-Codes)
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Whitelist Column -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h3 class="card-title fw-bold text-success">
                        Whitelist Management
                    </h3>
                </div>
                <div class="card-body px-4 pb-4">
                    <?php if ($whitelistMessage): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($whitelistMessage); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Vorname</label>
                                <input type="text" class="form-control" name="wl_firstname" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nachname</label>
                                <input type="text" class="form-control" name="wl_lastname" required>
                            </div>
                        </div>
                        <button type="submit" name="add_whitelist" class="btn btn-success w-100 py-2 fw-bold">
                            Zur Whitelist hinzufügen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
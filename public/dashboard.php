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

<!DOCTYPE html>
<html>
<head>
    <title>Admin-Bereich</title>
</head>
<body>

    <h1>Admin-Bereich</h1>

    <p>Willkommen, <?php echo htmlspecialchars($admin['username'] ?? ''); ?>!</p>
    <p><a href="logout.php">Abmelden</a></p>

    <hr>

    <h2>Einladungslinks generieren</h2>

    <form method="POST">
        <label>Anzahl Hauptgast-Links:</label>
        <input type="number" name="num_hauptgast" min="0" value="0"><br><br>

        <label>Anzahl +1-Links:</label>
        <input type="number" name="num_plusone" min="0" value="0"><br><br>

        <button type="submit" name="generate_invite">Links generieren</button>
    </form>

    <?php if (!empty($generatedHauptgast)): ?>
        <h3>Hauptgast-Links:</h3>
        <ul>
            <?php foreach ($generatedHauptgast as $link): ?>
                <li>
                    <input type="text" value="<?php echo htmlspecialchars($link); ?>" readonly size="60">
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($generatedPlusOne)): ?>
        <h3>+1-Links:</h3>
        <ul>
            <?php foreach ($generatedPlusOne as $link): ?>
                <li>
                    <input type="text" value="<?php echo htmlspecialchars($link); ?>" readonly size="60">
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <hr>

    <h2>Name zur Whitelist hinzufügen</h2>

    <?php if ($whitelistMessage): ?>
        <p><?php echo htmlspecialchars($whitelistMessage); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Vorname:</label>
        <input type="text" name="wl_firstname" required><br><br>

        <label>Nachname:</label>
        <input type="text" name="wl_lastname" required><br><br>

        <button type="submit" name="add_whitelist">Zur Whitelist hinzufügen</button>
    </form>

</body>
</html>
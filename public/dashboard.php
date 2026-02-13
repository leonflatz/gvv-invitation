<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../app/db.php';

// Fetch admin info
$stmt = $pdo->prepare("SELECT username FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$generatedHauptgast = [];
$generatedPlusOne = [];

// Handle invite link generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_invite'])) {
    $numHauptgast = (int) ($_POST['num_hauptgast'] ?? 0);
    $numPlusOne = (int) ($_POST['num_plusone'] ?? 0);

    // Helper function to generate links
    function generateLinks($pdo, $adminId, $type, $count) {
        $links = [];
        for ($i = 0; $i < $count; $i++) {
            $token = bin2hex(random_bytes(16)); // 32-character token
            $stmt = $pdo->prepare("INSERT INTO admin_links (admins_id, link, type) VALUES (?, ?, ?)");
            $stmt->execute([$adminId, $token, $type]);
            $links[] = "http://localhost/register.php?invite=" . $token; // change host later
        }
        return $links;
    }

    if ($numHauptgast > 0) {
        $generatedHauptgast = generateLinks($pdo, $_SESSION['admin_id'], 'Hauptgast', $numHauptgast);
    }
    if ($numPlusOne > 0) {
        $generatedPlusOne = generateLinks($pdo, $_SESSION['admin_id'], '+1 Link', $numPlusOne);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($admin['username']); ?>!</p>
    <p><a href="logout.php">Logout</a></p>

    <hr>

    <h2>Generate Invite Links</h2>
    <form method="POST" action="">
        <label>Number of Hauptgast Links:</label>
        <input type="number" name="num_hauptgast" min="0" value="0"><br><br>

        <label>Number of +1 Links:</label>
        <input type="number" name="num_plusone" min="0" value="0"><br><br>

        <button type="submit" name="generate_invite">Generate Links</button>
    </form>

    <?php if (!empty($generatedHauptgast)): ?>
        <h3>Hauptgast Links:</h3>
        <ul>
            <?php foreach ($generatedHauptgast as $link): ?>
                <li><input type="text" value="<?php echo htmlspecialchars($link); ?>" readonly size="60"></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($generatedPlusOne)): ?>
        <h3>+1 Links:</h3>
        <ul>
            <?php foreach ($generatedPlusOne as $link): ?>
                <li><input type="text" value="<?php echo htmlspecialchars($link); ?>" readonly size="60"></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</body>
</html>
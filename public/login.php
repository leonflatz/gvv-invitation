<?php
session_start();

require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/services/AdminService.php';

$service = new AdminService($pdo);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = $service->login($username, $password);

    if ($result['success']) {
        $_SESSION['admin_id'] = $result['admin_id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin-Anmeldung</title>
</head>
<body>

    <h1>Admin-Anmeldung</h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label>Benutzername:</label>
        <input type="text" name="username" required><br><br>

        <label>Passwort:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit">Anmelden</button>
    </form>

</body>
</html>
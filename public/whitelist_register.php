<?php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/services/WhitelistService.php';

$service = new WhitelistService($pdo);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $attendance_days = (int)($_POST['attendance_days'] ?? 0);

    $result = $service->register($firstname, $lastname, $attendance_days);

    if ($result['success']) {
        $success = 'Registrierung erfolgreich. Vielen Dank!';
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Whitelist-Registrierung</title>
</head>
<body>

<h1>Whitelist-Registrierung</h1>

<?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
<?php else: ?>

<form method="POST">

    <label>Vorname*:</label>
    <input type="text" name="firstname" required><br><br>

    <label>Nachname*:</label>
    <input type="text" name="lastname" required><br><br>

    <label>Anwesenheit*:</label>
    <select name="attendance_days" required>
        <option value="">-- Bitte auswählen --</option>
        <option value="1">Freitag</option>
        <option value="2">Samstag</option>
        <option value="3">Freitag und Samstag</option>
    </select><br><br>

    <button type="submit">Registrieren</button>

</form>

<?php endif; ?>

</body>
</html>
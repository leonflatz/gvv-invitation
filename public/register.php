<?php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/services/RegistrationService.php';

$service = new RegistrationService($pdo);

$inviteToken = $_GET['invite'] ?? '';
$error = '';
$success = '';
$linkType = '';

$validation = $service->validateInvite($inviteToken);

if (!$validation['valid']) {
    die($validation['message']);
}

$linkType = $validation['invite']['type'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $result = $service->register($_POST, $inviteToken, $linkType);

    if ($result['success']) {
        $success = "Registrierung erfolgreich. Vielen Dank!";
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registrierung</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>

<h1>Registrierung</h1>

<?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
<?php else: ?>

<form method="POST">

    <label>Vorname*:</label>
    <input class="form-input" type="text" name="firstname" required><br><br>

    <label>Nachname*:</label>
    <input type="text" name="lastname" required><br><br>

    <?php if ($linkType === '+1 Link'): ?>

        <label>Vorname Hauptgast*:</label>
        <input type="text" name="firstname_mainguest" required><br><br>

        <label>Nachname Hauptgast*:</label>
        <input type="text" name="lastname_mainguest" required><br><br>

    <?php endif; ?>

    <label>Anwesenheit*:</label>
    <select name="attendance_days" required>
        <option value="">-- Bitte auswählen --</option>
        <option value="1">Freitag</option>
        <option value="2">Samstag</option>
        <option value="3">Beide Tage</option>
    </select><br><br>

    <button type="submit">Registrieren</button>

</form>

<?php endif; ?>

</body>
</html>
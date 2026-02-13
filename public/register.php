<?php
require_once __DIR__ . '/../app/db.php';

$inviteToken = $_GET['invite'] ?? '';
$error = '';
$success = '';

// 1️⃣ Check if invite token exists in admin_links
$stmt = $pdo->prepare("SELECT * FROM admin_links WHERE link = ?");
$stmt->execute([$inviteToken]);
$invite = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invite) {
    die('Invalid invite link.');
}

// 2️⃣ Check if this link has already been used
$stmt = $pdo->prepare("SELECT * FROM invited_users WHERE invite_link = ?");
$stmt->execute([$inviteToken]);
$used = $stmt->fetch(PDO::FETCH_ASSOC);

if ($used) {
    die('This invite link has already been used.');
}

$linkType = $invite['type']; // 'Hauptgast' or '+1 Link'

// 3️⃣ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $firstname_mainguest = trim((string)($_POST['firstname_mainguest'] ?? ''));
    $lastname_mainguest = trim((string)($_POST['lastname_mainguest'] ?? ''));
    $attendance_days = $_POST['attendance_days'] ?? '';

    // Basic required validation
    if (!$firstname || !$lastname || !$attendance_days) {
        $error = "Please fill in all mandatory fields.";
    } elseif ($linkType === '+1 Link') {
        if (!$firstname_mainguest || !$lastname_mainguest) {
            $error = "Please fill in the Hauptgast fields for +1 registration.";
        } else {
            // Check that the Hauptgast exists and is unique
            $stmt = $pdo->prepare("
                SELECT COUNT(*) AS count
                FROM invited_users
                WHERE firstname = ? 
                  AND lastname = ? 
                  AND firstname_mainguest IS NULL 
                  AND lastname_mainguest IS NULL
            ");
            $stmt->execute([$firstname_mainguest, $lastname_mainguest]);
            $hauptgastCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            if ($hauptgastCount == 0) {
                $error = "The Hauptgast reference is invalid or not unique. +1 registration rejected.";
            }

            $stmt = $pdo->prepare("
                SELECT COUNT(*) AS count
                FROM invited_users
                WHERE firstname_mainguest = ? 
                  AND lastname_mainguest = ?
            ");
            $stmt->execute([$firstname_mainguest, $lastname_mainguest]);
            $plusoneCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            if ($plusoneCount >= 1) {
                $error = "The Hauptgast already has a +1 guest registered.";
            }
        }
    } else {
        // Hauptgast link: ignore mainguest fields
        $firstname_mainguest = null;
        $lastname_mainguest = null;
    }

    // If no errors, insert user
    if (!$error) {
        $stmt = $pdo->prepare("
            INSERT INTO invited_users
            (invite_link, firstname, lastname, firstname_mainguest, lastname_mainguest, attendance_days)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$inviteToken, $firstname, $lastname, $firstname_mainguest, $lastname_mainguest, $attendance_days]);
        $success = "Registration successful! Thank you.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
</head>
<body>
    <h1>User Registration</h1>

    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if ($success) {
        echo "<p style='color:green;'>$success</p>";
    } else { ?>
        <form method="POST" action="">
            <label>First Name*:</label>
            <input type="text" name="firstname" required><br><br>

            <label>Last Name*:</label>
            <input type="text" name="lastname" required><br><br>

            <?php if ($linkType === '+1 Link'): ?>
                <label>Hauptgast First Name*:</label>
                <input type="text" name="firstname_mainguest" required><br><br>

                <label>Hauptgast Last Name*:</label>
                <input type="text" name="lastname_mainguest" required><br><br>
            <?php endif; ?>

            <label>Attendance Days*:</label>
            <select name="attendance_days" required>
                <option value="">--Select--</option>
                <option value="1">Fr</option>
                <option value="2">Sa</option>
                <option value="3">Beide</option>
            </select><br><br>

            <button type="submit">Register</button>
        </form>
    <?php } ?>

</body>
</html>
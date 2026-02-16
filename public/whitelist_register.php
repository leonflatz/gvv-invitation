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

<?php include '../templates/header.php'; ?>

<style>
    body {
        background: url('./img/riad-grafiti-sc.png') no-repeat center center fixed; 
        background-size: cover;
    }
    .card-alive {
        background: rgba(255, 255, 255, 0.95);
    }
</style>

<div class="row justify-content-center my-5">
    <div class="col-md-8 col-lg-6">
        <div class="card card-alive shadow-lg border-0">
            <div class="card-header card-alive-header text-center py-4">
                <h2 class="mb-0 fw-bold">Whitelist Registrierung</h2>
            </div>
            <div class="card-body p-4 p-md-5">

                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <h4 class="alert-heading">Vielen Dank!</h4>
                        <p><?php echo htmlspecialchars($success); ?></p>
                        <hr>
                        <p class="mb-0">Wir prüfen Ihre Anfrage.</p>
                    </div>
                <?php else: ?>

                <form method="POST" class="needs-validation" novalidate>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="firstname" class="form-label">Vorname*</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="lastname" class="form-label">Nachname*</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>

                        <div class="col-12 mt-4">
                            <label for="attendance_days" class="form-label">Anwesenheit*</label>
                            <select class="form-select" id="attendance_days" name="attendance_days" required>
                                <option value="" selected disabled>-- Bitte auswählen --</option>
                                <option value="1">Freitag</option>
                                <option value="2">Samstag</option>
                                <option value="3">Beide Tage</option>
                            </select>
                        </div>

                        <div class="col-12 mt-4 d-grid">
                            <button type="submit" class="btn btn-alive btn-lg">Jetzt Registrieren</button>
                        </div>
                    </div>
                </form>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
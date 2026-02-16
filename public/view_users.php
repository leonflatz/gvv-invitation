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

    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Suchen nach Namen...">
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0" id="usersTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4" style="cursor:pointer;" onclick="sortTable(0)">Vorname &#x2195;</th>
                            <th class="py-3 px-4" style="cursor:pointer;" onclick="sortTable(1)">Nachname &#x2195;</th>
                            <th class="py-3 px-4" style="cursor:pointer;" onclick="sortTable(2)">Vorname Hauptgast &#x2195;</th>
                            <th class="py-3 px-4" style="cursor:pointer;" onclick="sortTable(3)">Nachname Hauptgast &#x2195;</th>
                            <th class="py-3 px-4" style="cursor:pointer;" onclick="sortTable(4)">Anwesenheit &#x2195;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Keine Registrierungen gefunden.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
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

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    var input = document.getElementById('searchInput');
    var filter = input.value.toLowerCase();
    var table = document.getElementById('usersTable');
    var tr = table.getElementsByTagName('tr');

    for (var i = 1; i < tr.length; i++) {
        var td = tr[i].getElementsByTagName('td');
        var found = false;
        // Loop through name columns (0 and 1)
        for (var j = 0; j < 2; j++) {
            if (td[j]) {
                if (td[j].textContent.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        tr[i].style.display = found ? "" : "none";
    }
});

function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("usersTable");
  switching = true;
  dir = "asc"; 
  while (switching) {
    switching = false;
    rows = table.rows;
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchcount ++;      
    } else {
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
</script>

<?php include '../templates/footer.php'; ?>
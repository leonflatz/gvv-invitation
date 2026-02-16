<?php
// header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Genussvereinigung Vorarlberg</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css?v=<?=time()?>">
</head>
<body>

<header class="site-header sticky-top">
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand header-title fs-3" href="index.php">
                Genussvereinigung Vorarlberg
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button> 
            
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-3">
                    <li class="nav-item">
                        <a href="whitelist_register.php" class="nav-link fw-bold text-dark">Anmeldung Riad OpenAir</a>
                    </li>
                    <li class="nav-item">
                        <?php if (isset($_SESSION['admin_id'])): ?>
                            <a href="dashboard.php" class="btn btn-alive">Dashboard</a>
                        <?php else: ?>
                            <button type="button" class="btn btn-outline-alive" data-bs-toggle="modal" data-bs-target="#loginModal">
                                Admin Login
                            </button>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="loginModalLabel">Admin Login</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if (isset($_GET['login_error'])): ?>
            <div class="alert alert-danger" role="alert">
                Anmeldung fehlgeschlagen. Bitte überprüfen Sie Ihre Daten.
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var myModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    myModal.show();
                });
            </script>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Benutzername</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Passwort</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-alive">Anmelden</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<main class="site-content">
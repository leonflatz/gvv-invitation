<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<?php include '../templates/header.php'; ?>

<section class="hero-section" style="background-image: url('./img/riad-grafiti-sc.png');">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <h1 class="display-3 fw-bold mb-4 text-white">Willkommen beim<br>Riad OpenAir</h1>
        <p class="lead mb-5 text-white-75">Sichern Sie sich jetzt Ihren Platz für das Event des Jahres.</p>
        
        <a href="whitelist_register.php" class="btn btn-alive btn-lg px-5 py-3 fs-4 shadow-lg">
            Anmeldung Riad OpenAir
        </a>
    </div>
</section>

<?php include '../templates/footer.php'; ?>
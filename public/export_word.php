<?php
// export_word.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$links = [];
// Check if we have specific links passed (e.g. from dashboard generation)
if (isset($_POST['links']) && is_array($_POST['links'])) {
    $links = array_merge($links, $_POST['links']);
}
if (isset($_POST['hauptgast_links']) && is_array($_POST['hauptgast_links'])) {
    $links = array_merge($links, $_POST['hauptgast_links']);
}
if (isset($_POST['plusone_links']) && is_array($_POST['plusone_links'])) {
    $links = array_merge($links, $_POST['plusone_links']);
}

if (empty($links)) {
    die("Keine Links zum Exportieren gefunden.");
}

// Group links by type
$hauptgastLinks = [];
$plusOneLinks = [];

foreach ($links as $link) {
    if (strpos($link, 'type=Hauptgast') !== false || strpos($link, 'Hauptgast') !== false) {
       $hauptgastLinks[] = $link;
    } else {
       // Assume everything else is +1 or check explicitly
       $plusOneLinks[] = $link;
    }
    // Note: The previous logic in DashboardService didn't attach type to the URL query param, 
    // it just returned "register.php?invite=...".
    // We need to fetch the type from DB or pass it differently. 
    // FOR NOW: The dashboard.php doesn't distinguish them in the POST to this script easily unless we structure it.
    // Let's assume the user selects which batch to export or we export all recent.
    
    // Correction: The Dashboard shows them in separate lists. We should probably just print them all, 
    // but the user asked for "visibly separating hauptgast and +1 Links".
    // Since the URLs don't have the type, and we don't want to query DB for every link here if possible,
    // let's try to pass the keys or structure from dashboard.
}

// Headers for Word
header("Content-Type: application/msword");
header("Content-Disposition: attachment; filename=Einladungslinks.doc");
?>
<html>
<head>
<style>
    body { font-family: Arial, sans-serif; }
    .page-break { page-break-after: always; }
    .link-container { margin-bottom: 30px; border-bottom: 1px solid #ccc; padding-bottom: 20px; }
    h1 { color: #ff512f; }
    h2 { background-color: #eee; padding: 5px; }
    .qr-code { width: 150px; height: 150px; }
</style>
</head>
<body>

<h1>Generierte Einladungslinks</h1>
<p>Exportiert am: <?php echo date('d.m.Y H:i'); ?></p>

<?php if (isset($_POST['hauptgast_links']) && is_array($_POST['hauptgast_links'])): ?>
    <h2>Hauptgast Links</h2>
    <?php foreach ($_POST['hauptgast_links'] as $link): ?>
        <?php 
            $fullUrl = (strpos($link, 'http') === 0) ? $link : "http://localhost:8080/public/" . $link; 
            // Note: In a real scenario, use actual domain.
        ?>
        <div class="link-container">
            <p><strong>Link:</strong> <a href="<?php echo $fullUrl; ?>"><?php echo $fullUrl; ?></a></p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($fullUrl); ?>" class="qr-code">
        </div>
    <?php endforeach; ?>
    <div class="page-break"></div>
<?php endif; ?>

<?php if (isset($_POST['plusone_links']) && is_array($_POST['plusone_links'])): ?>
    <h2>+1 Links</h2>
    <?php foreach ($_POST['plusone_links'] as $link): ?>
        <?php 
            $fullUrl = (strpos($link, 'http') === 0) ? $link : "http://localhost:8080/public/" . $link; 
        ?>
        <div class="link-container">
            <p><strong>Link:</strong> <a href="<?php echo $fullUrl; ?>"><?php echo $fullUrl; ?></a></p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($fullUrl); ?>" class="qr-code">
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>

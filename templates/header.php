<?php
// header.php
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Einladungsverwaltung</title>
    <link rel="stylesheet" href="css/style.css?v=<?=time()?>">
</head>
<body>

<header class="site-header">
    <div class="container header-container">
        <h1 class="header-title">Einladungsverwaltung</h1>

        <div class="header-buttons">
            <a href="index.php" class="btn btn-login">Admin Login</a>
            <a href="whitelist_register.php" class="btn btn-whitelist">Whitelist Registrierung</a>
        </div>
    </div>
</header>

<main class="site-content">
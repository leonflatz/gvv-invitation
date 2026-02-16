<?php

class DashboardService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAdminById($adminId)
    {
        $stmt = $this->pdo->prepare("SELECT username FROM admins WHERE id = ?");
        $stmt->execute([$adminId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function generateInviteLinks($adminId, $type, $count)
    {
        $links = [];

        for ($i = 0; $i < $count; $i++) {
            $token = bin2hex(random_bytes(16));

            $stmt = $this->pdo->prepare("
                INSERT INTO admin_links (admins_id, link, type)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$adminId, $token, $type]);

            $links[] = "register.php?invite=" . $token;
        }

        return $links;
    }

    public function addToWhitelist($firstname, $lastname, $adminUsername)
    {
        if (!$firstname || !$lastname) {
            return "Vor- und Nachname müssen ausgefüllt werden.";
        }

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO whitelist (firstname, lastname, admin_username)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$firstname, $lastname, $adminUsername]);

            return "Person wurde erfolgreich zur Whitelist hinzugefügt.";
        } catch (PDOException $e) {
            return "Diese Person steht bereits auf der Whitelist.";
        }
    }
}
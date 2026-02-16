<?php

class WhitelistService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function register($firstname, $lastname, $attendanceDays)
    {
        if (!$firstname || !$lastname || !$attendanceDays) {
            return [
                'success' => false,
                'message' => 'Bitte alle Pflichtfelder ausfüllen.'
            ];
        }

        // 1️⃣ Prüfen, ob auf Whitelist
        $stmt = $this->pdo->prepare("
            SELECT admin_username
            FROM whitelist
            WHERE firstname = ? AND lastname = ?
        ");
        $stmt->execute([$firstname, $lastname]);
        $whitelistEntry = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$whitelistEntry) {
            return [
                'success' => false,
                'message' => 'Sie stehen nicht auf der Whitelist.'
            ];
        }

        // 2️⃣ Prüfen ob bereits registriert
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM invited_users
            WHERE firstname = ?
              AND lastname = ?
              AND firstname_mainguest IS NULL
              AND lastname_mainguest IS NULL
        ");
        $stmt->execute([$firstname, $lastname]);

        if ((int)$stmt->fetchColumn() >= 1) {
            return [
                'success' => false,
                'message' => 'Sie sind bereits registriert.'
            ];
        }

        // 3️⃣ Admin-ID ermitteln
        $stmt = $this->pdo->prepare("
            SELECT id FROM admins WHERE username = ?
        ");
        $stmt->execute([$whitelistEntry['admin_username']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            return [
                'success' => false,
                'message' => 'Zugehöriger Administrator konnte nicht gefunden werden.'
            ];
        }

        $adminId = $admin['id'];

        // 4️⃣ Internen Token erzeugen
        $token = 'WHITELIST-' . bin2hex(random_bytes(16));

        $stmt = $this->pdo->prepare("
            INSERT INTO admin_links (admins_id, link, type)
            VALUES (?, ?, 'Hauptgast')
        ");
        $stmt->execute([$adminId, $token]);

        // 5️⃣ Registrierung eintragen
        $stmt = $this->pdo->prepare("
            INSERT INTO invited_users
            (invite_link, firstname, lastname, firstname_mainguest, lastname_mainguest, attendance_days)
            VALUES (?, ?, ?, NULL, NULL, ?)
        ");

        $stmt->execute([
            $token,
            $firstname,
            $lastname,
            $attendanceDays
        ]);

        return ['success' => true];
    }
}
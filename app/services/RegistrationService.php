<?php

class RegistrationService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function validateInvite($token)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM admin_links WHERE link = ?");
        $stmt->execute([$token]);
        $invite = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invite) {
            return ['valid' => false, 'message' => 'Ungültiger Einladungslink.'];
        }

        $stmt = $this->pdo->prepare("SELECT id FROM invited_users WHERE invite_link = ?");
        $stmt->execute([$token]);

        if ($stmt->fetch()) {
            return ['valid' => false, 'message' => 'Dieser Einladungslink wurde bereits verwendet.'];
        }

        return ['valid' => true, 'invite' => $invite];
    }

    public function register($data, $inviteToken, $linkType)
    {
        $firstname = trim($data['firstname'] ?? '');
        $lastname  = trim($data['lastname'] ?? '');
        $attendance_days = $data['attendance_days'] ?? '';

        $firstname_mainguest = trim((string)($data['firstname_mainguest'] ?? ''));
        $lastname_mainguest  = trim((string)($data['lastname_mainguest'] ?? ''));

        if (!$firstname || !$lastname || !$attendance_days) {
            return ['success' => false, 'message' => 'Bitte alle Pflichtfelder ausfüllen.'];
        }

        if ($linkType === '+1 Link') {

            if (!$firstname_mainguest || !$lastname_mainguest) {
                return ['success' => false, 'message' => 'Bitte Hauptgast vollständig angeben.'];
            }

            // Prüfen ob Hauptgast existiert
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) 
                FROM invited_users
                WHERE firstname = ?
                  AND lastname = ?
                  AND firstname_mainguest IS NULL
                  AND lastname_mainguest IS NULL
            ");
            $stmt->execute([$firstname_mainguest, $lastname_mainguest]);
            $count = $stmt->fetchColumn();

            if ($count != 1) {
                return ['success' => false, 'message' => 'Hauptgast existiert nicht oder ist nicht eindeutig.'];
            }

            // Prüfen ob bereits +1 existiert
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*)
                FROM invited_users
                WHERE firstname_mainguest = ?
                  AND lastname_mainguest = ?
            ");
            $stmt->execute([$firstname_mainguest, $lastname_mainguest]);

            if ($stmt->fetchColumn() >= 1) {
                return ['success' => false, 'message' => 'Für diesen Hauptgast ist bereits eine Begleitperson registriert.'];
            }

        } else {
            $firstname_mainguest = null;
            $lastname_mainguest  = null;
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO invited_users
            (invite_link, firstname, lastname, firstname_mainguest, lastname_mainguest, attendance_days)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $inviteToken,
            $firstname,
            $lastname,
            $firstname_mainguest,
            $lastname_mainguest,
            $attendance_days
        ]);

        return ['success' => true];
    }
}
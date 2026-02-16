<?php

class AdminService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function login($username, $password)
    {
        if (!$username || !$password) {
            return [
                'success' => false,
                'message' => 'Benutzername und Passwort sind erforderlich.'
            ];
        }

        $stmt = $this->pdo->prepare("
            SELECT id, username, password
            FROM admins
            WHERE username = ?
        ");
        $stmt->execute([$username]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($password, $admin['password'])) {
            return [
                'success' => false,
                'message' => 'Benutzername oder Passwort ist falsch.'
            ];
        }

        return [
            'success' => true,
            'admin_id' => $admin['id']
        ];
    }
}
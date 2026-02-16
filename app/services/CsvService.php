<?php

class CsvService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function exportRegisteredUsers()
    {
        $stmt = $this->pdo->query("
            SELECT iu.*, a.username AS admin_username
            FROM invited_users iu
            JOIN admin_links al ON iu.invite_link = al.link
            JOIN admins a ON al.admins_id = a.id
            ORDER BY iu.id ASC
        ");

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="registrierte_personen.csv"');

        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM für Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Kopfzeile
        fputcsv(
            $output,
            [
                'Einladender Admin', 
                'Vorname', 
                'Nachname', 
                'Vorname Hauptgast', 
                'Nachname Hauptgast', 
                'Anwesenheit'
            ],
            ';',
            '"',
            '\\'
        );

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $days = $this->attendanceLabel($row['attendance_days']);

            fputcsv(
                $output,
                [
                    (string)$row['admin_username'],
                    (string)$row['firstname'],
                    (string)$row['lastname'],
                    (string)($row['firstname_mainguest'] ?? ''),
                    (string)($row['lastname_mainguest'] ?? ''),
                    $days
                ],
                ';',
                '"',
                '\\'
            );
        }

        fclose($output);
        exit;
    }

    public function getAllUsers()
    {
        $stmt = $this->pdo->query("
            SELECT * FROM invited_users
            ORDER BY id ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function attendanceLabel($num)
    {
        return match ((int)$num) {
            1 => 'Freitag',
            2 => 'Samstag',
            3 => 'Freitag und Samstag',
            default => ''
        };
    }
}
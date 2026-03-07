<?php
require_once __DIR__ . "/database.php";

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(string $username, string $password, string $fullName, string $role = "user"): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare(
            "INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)"
        );

        return $stmt->execute([$username, $hash, $fullName, $role]);
    }

    public function updateTheme(int $userId, string $theme): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET theme=? WHERE id=?");
        return $stmt->execute([$theme, $userId]);
    }
}

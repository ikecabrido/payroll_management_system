<?php
require_once __DIR__ . "/user.php";

class Auth
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function attempt(string $username, string $password): bool
    {
        $user = $this->userModel->findByUsername($username);

        if (!$user) {
            return false;
        }


        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Login success → store minimal session info
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'role' => $user['role'],
            'theme' => $user['theme'],
        ];

        return true;
    }

    public function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public function logout(): void
    {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}

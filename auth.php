<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

function require_role(string $role): void
{
    require_login();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header('HTTP/1.1 403 Forbidden');
        echo "Access denied.";
        exit();
    }
}

function current_user(): array
{
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'role' => $_SESSION['role'] ?? null,
    ];
}

function is_admin(): bool
{
    return ($_SESSION['role'] ?? '') === 'admin';
}


<?php

namespace App\Models;

use \PDO;

class UserModel extends Database
{
    public function register(string $name, string $username, string $email, string $password): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("CALL register_user(?, ?, ?, ?)");
        return $stmt->execute([$name, $username, $email, $hashedPassword]);
    }

    public function getUserByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("CALL login_user(?)");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getUserById($id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAllAuthors()
    {
        $stmt = $this->pdo->prepare("SELECT id, username, name, email FROM users WHERE role IN ('admin', 'editor') ORDER BY role DESC, name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setRememberToken(int $userId, string $token, string $expiresAt): bool
    {
        $stmt = $this->pdo->prepare("CALL set_remember_token(?, ?, ?)");
        return $stmt->execute([$userId, $token, $expiresAt]);
    }

    public function getUserByRememberToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare("CALL get_user_by_remember_token(?)");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function setPasswordResetToken(int $userId, string $token, string $expiresAt): bool
    {
        $stmt = $this->pdo->prepare("CALL set_password_reset_token(?, ?, ?)");
        return $stmt->execute([$userId, $token, $expiresAt]);
    }

    public function checkPasswordResetToken(string $token): bool
    {
        $stmt = $this->pdo->prepare("CALL check_password_reset_token(?)");
        $stmt->execute([$token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['valid'] > 0;
    }

    public function getUserByPasswordResetToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare("CALL get_user_by_reset_token(?)");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function clearPasswordResetToken(int $userId): bool
    {
        $stmt = $this->pdo->prepare("CALL clear_password_reset_token(?)");
        return $stmt->execute([$userId]);
    }

    public function updatePassword(int $userId, string $newPassword): bool
    {
        $stmt = $this->pdo->prepare("CALL update_user_password(?, ?)");
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $stmt->execute([$userId, $hashedPassword]);
    }
}

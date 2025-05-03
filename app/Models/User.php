<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{

    public function findByEmail(string $email)
    {
        return $this->db->query("SELECT * FROM users WHERE email = :email", [
            'email' => $email
        ])->fetch();
    }

    public function validateLogin(string $email, string $password)
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        return $user;
    }

    public function register(string $name, string $email, string $password, string $role = 'customer'): \PDOStatement
    {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        // Check if email exists
        if ($this->findByEmail($email)) {
            throw new \Exception('Email already exists');
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        return $this->db->query("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)", [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role
        ]);
    }

    public function getActiveUsers(?string $role = null)
    {
        return $this->db->query("SELECT * FROM users WHERE is_active = :is_active AND role = :role", [
            'is_active' => 1,
            'role' => $role
        ])->fetchAll();
    }

    public function updatePassword(int $userId, string $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->db->query("UPDATE users SET password = :password WHERE id = :id", [
            'password' => $hashedPassword,
            'id' => $userId
        ]);
    }
}

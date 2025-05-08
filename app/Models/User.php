<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{

    public function findByEmail(string $email)
    {
        return $this->db->query("SELECT * FROM users WHERE email = :email LIMIT 1", [
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

    public function register(string $name, string $email, string $password, ?string $phoneNumber = null, string $userType = 'customer'): \PDOStatement
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

        return $this->db->query("INSERT INTO users (name, email, password, phone_number, user_type) VALUES (:name, :email, :password, :phoneNumber, :userType)", [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'phoneNumber' => $phoneNumber,
            'userType' => $userType
        ]);
    }

    public function getActiveUsers(?string $userType = null)
    {
        $query = "SELECT * FROM users";
        $params = [];

        if ($userType) {
            $query .= " WHERE user_type = :userType";
            $params['userType'] = $userType;
        }

        return $this->db->query($query, $params)->fetchAll();
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

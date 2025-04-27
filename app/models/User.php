<?php
namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected $table = 'users';

    public function findByEmail($email) {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
    }

    public function validateLogin($email, $password) {
        $user = $this->findByEmail($email);

        if (!$user) {
            return false;
        }

        return password_verify($password, $user['password']);
    }

    public function register($name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        return $this->create([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword
        ]);
    }
}

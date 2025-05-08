<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private int $cookieExpiration = 604800; // 7 days

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->render('auth/register');
        }

        $name = trim($_POST['name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        // Validate inputs
        $errors = [];
        if (empty($name)) {
            $errors[] = 'Nama harus diisi.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password harus minimal 8 karakter.';
        }
        if ($password !== $passwordConfirmation) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }

        if (!empty($errors)) {
            $_SESSION['registration_errors'] = $errors;
            $_SESSION['old_input'] = ['name' => $name, 'email' => $email];
            $this->redirect('/register');
            return;
        }

        try {
            new User()->register($name, $email, $password);
            $_SESSION['registration_success'] = 'Pendaftaran berhasil! Silakan masuk ke akun Anda.';
            $this->redirect('/login');
        } catch (\Exception $e) {
            $_SESSION['registration_error'] = 'Pendaftaran gagal: ' . $e->getMessage();
            $_SESSION['old_input'] = ['name' => $name, 'email' => $email];
            $this->redirect('/register');
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->render('auth/login');
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        $redirect = filter_var($_POST['redirect'] ?? '/dashboard', FILTER_SANITIZE_URL);

        // Validate inputs
        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Email dan password harus diisi.';
            $_SESSION['old_input'] = ['email' => $email];
            $this->redirect('/login');
            return;
        }

        $user = new User()->validateLogin($email, $password);

        if ($user) {
            $_SESSION = [
                'user_id' => $user['id'],
                'user_name' => $user['name'],
                'user_role' => $user['user_type'],
                'login_success' => 'Login berhasil! Selamat datang kembali, ' . $user['name'] . '.'
            ];

            if ($remember) {
                $this->saveSessionToCookie($user['id']);
            }

            $this->redirect($redirect);
        } else {
            $_SESSION['login_error'] = 'Email atau password tidak valid.';
            $_SESSION['old_input'] = ['email' => $email];
            $this->redirect('/login');
        }
    }

    public function logout()
    {
        $this->clearSessionCookie();
        session_destroy();
        $this->redirect('/login');
    }

    private function saveSessionToCookie(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + $this->cookieExpiration;
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        // Store token in database (implementation needed)
        // new User()->storeRememberToken($userId, $token, $expiry);

        setcookie('remember_token', $token, $expiry, '/', '', $secure, true);
        setcookie('remember_user_id', $userId, $expiry, '/', '', $secure, true);
    }

    private function clearSessionCookie(): void
    {
        if (isset($_COOKIE['remember_token'])) {
            // Remove token from database (implementation needed)
            // new User()->removeRememberToken($_COOKIE['remember_token']);
        }

        setcookie('remember_token', '', time() - 3600, '/', '', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', true);
        setcookie('remember_user_id', '', time() - 3600, '/', '', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', true);
    }

    public function resetPasswordRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->render('auth/reset_request');
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['reset_error'] = 'Email tidak valid.';
            $this->redirect('/reset-password');
            return;
        }

        try {
            // Implementation needed in User model
            // new User()->sendPasswordResetEmail($email);
            $_SESSION['reset_success'] = 'Instruksi reset password telah dikirim ke email Anda.';
            $this->redirect('/login');
        } catch (\Exception $e) {
            $_SESSION['reset_error'] = 'Tidak dapat mengirim email reset: ' . $e->getMessage();
            $this->redirect('/reset-password');
        }
    }
}
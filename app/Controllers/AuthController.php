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

        $errors = $this->validateRegistration($name, $email, $password, $passwordConfirmation);

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->toast(['error' => ['title' => 'Pendaftaran Gagal', 'message' => $error]]);
            }

            $_SESSION['old_input'] = ['name' => $name, 'email' => $email];
            return $this->redirect('/register');
        }

        try {
            (new User())->register($name, $email, $password);
            $this->toast(['success' => ['title' => 'Pendaftaran Berhasil', 'message' => 'Silakan masuk ke akun Anda.']]);
            $this->redirect('/login');
        } catch (\Exception $e) {
            $this->toast(['error' => ['title' => 'Terjadi Kesalahan', 'message' => 'Pendaftaran gagal: ' . $e->getMessage()]]);
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

        if (empty($email) || empty($password)) {
            $this->toast(['error' => ['title' => 'Login Gagal', 'message' => 'Email dan password harus diisi.']]);
            $_SESSION['old_input'] = ['email' => $email];
            return $this->redirect('/login');
        }

        $user = (new User())->validateLogin($email, $password);

        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['user_type']
            ];


            $this->toast(['success' => ['title' => 'Login Berhasil', 'message' => 'Selamat datang kembali, ' . $user['name'] . '.']]);

            if ($remember) {
                $this->saveSessionToCookie($user['id']);
            }

            $this->redirect($redirect);
        } else {
            $this->toast(['error' => ['title' => 'Login Gagal', 'message' => 'Email atau password tidak valid.']]);
            $_SESSION['old_input'] = ['email' => $email];
            $this->redirect('/login');
        }
    }

    public function logout()
    {
        $this->clearSessionCookie();
        session_destroy();
        $this->toast(['info' => ['title' => 'Keluar', 'message' => 'Anda telah keluar dari akun.']]);
        $this->redirect('/login');
    }

    private function validateRegistration($name, $email, $password, $passwordConfirmation): array
    {
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

        return $errors;
    }

    private function saveSessionToCookie(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + $this->cookieExpiration;
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        setcookie('remember_token', $token, $expiry, '/', '', $secure, true);
        setcookie('remember_user_id', $userId, $expiry, '/', '', $secure, true);

        // TODO: Store token in DB for persistent login
    }

    private function clearSessionCookie(): void
    {
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        setcookie('remember_token', '', time() - 3600, '/', '', $secure, true);
        setcookie('remember_user_id', '', time() - 3600, '/', '', $secure, true);

        // TODO: Remove token from DB
    }
}

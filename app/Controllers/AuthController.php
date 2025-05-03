<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private int $cookieExpiration = 3600 * 24 * 7; // 7 days

    public function register()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->render('auth/register');
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        $userModel = new User();

        try {
            if ($password !== $passwordConfirmation) {
                throw new \InvalidArgumentException('Passwords do not match.');
            }

            $userModel->register($name, $email, $password);
            $_SESSION['registration_success'] = 'Registration successful! You can now log in.';
            header('Location: /login');
            exit;
        } catch (\InvalidArgumentException $e) {
            $_SESSION['registration_error'] = $e->getMessage();
        } catch (\Exception $e) {
            $_SESSION['registration_error'] = 'Registration failed: ' . $e->getMessage();
        }

        header('Location: /register');
        exit;
    }

    public function login()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->render('auth/login');
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = $_POST['remember'] ?? null;

        $userModel = new User();
        $user = $userModel->validateLogin($email, $password);

        if ($user) {
            $_SESSION = [
                'user_id' => $user['id'],
                'user_name' => $user['name'],
                'user_role' => $user['role'],
                'login_success' => 'Login successful!'
            ];

            if ($remember) {
                $this->saveSessionToCookie($user['id']);
            }

            echo $_POST;

            $_SESSION["registration_success"] = 'Login successful! Welcome back, ' . $user['name'] . '.';
            header('Location: /dashboard');
        } else {
            $_SESSION['login_error'] = 'Invalid email or password.';
            header('Location: /login');
        }
        exit;
    }

    public function logout()
    {
        session_start();
        $this->clearSessionCookie();
        session_destroy();
        header('Location: /login');
        exit;
    }

    private function saveSessionToCookie(int $userId): void
    {
        $token = bin2hex(random_bytes(32)); // Generate a unique token
        $expiry = time() + $this->cookieExpiration;
        $path = '/';
        $domain = $_SERVER['HTTP_HOST']; // Consider making this configurable
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'; // Send only over HTTPS
        $httponly = true; // Prevent JavaScript access

        // You would typically store this token and user ID in your database
        // for persistent login across sessions. For simplicity here, we'll just
        // store the user ID in the cookie (less secure for production).
        setcookie('remember_user_id', $userId, $expiry, $path, $domain, $secure, $httponly);
        // In a real application, store the token in the database associated with the user.
        // setcookie('remember_token', $token, $expiry, $path, $domain, $secure, $httponly);
    }

    public function checkRememberMe(): void
    {
        session_start();
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user_id'])) {
            $userId = $_COOKIE['remember_user_id'];
            $userModel = new User();
            $user = $userModel->findById($userId); // Assuming you have a find method

            if ($user) {
                $_SESSION = [
                    'user_id' => $user['id'],
                    'user_name' => $user['name'],
                    'user_role' => $user['role'],
                    'login_success' => 'Logged in via remember me!'
                ];
                // Optionally regenerate the cookie for a new expiration time
                $this->saveSessionToCookie($user['id']);
            } else {
                $this->clearSessionCookie();
            }
        }
    }

    private function clearSessionCookie(): void
    {
        $path = '/';
        $domain = $_SERVER['HTTP_HOST'];
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $httponly = true;

        setcookie('remember_user_id', '', time() - 3600, $path, $domain, $secure, $httponly);
        // In a real application, you'd also clear the 'remember_token' cookie
        // and invalidate the corresponding token in the database.
        // setcookie('remember_token', '', time() - 3600, $path, $domain, $secure, $httponly);
    }
}
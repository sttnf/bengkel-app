<?php

namespace App\Helpers;

class Toast
{

    public static function addToast(array $toast): void
    {
        if (!isset($_SESSION['toasts'])) {
            $_SESSION['toasts'] = [];
        }

        $_SESSION['toasts'][] = $toast;
    }

    public static function getToasts(): array
    {
        return $_SESSION['toasts'] ?? [];
    }

    public static function clearToasts(): void
    {
        unset($_SESSION['toasts']);
    }

}
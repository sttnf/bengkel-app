<?php

namespace App\Core;

use JetBrains\PhpStorm\NoReturn;

abstract class Controller
{
    /**
     * @throws \Exception
     */
    protected function render($view, $data = [], $layout = "main"): string
    {
        $router = new Router();
        return $router->renderView($view, $data, $layout);
    }

    #[NoReturn] protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    #[NoReturn] protected function json($data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
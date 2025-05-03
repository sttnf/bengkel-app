<?php
namespace App\Core;

use JetBrains\PhpStorm\NoReturn;

abstract class Controller {
    protected function render($view, $data = []): string
    {
        $router = new Router();
        return $router->renderView($view, $data);
    }

    #[NoReturn] protected function redirect($url) {
        header("Location: $url");
        exit;
    }

    #[NoReturn] protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
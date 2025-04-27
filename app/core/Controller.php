<?php
namespace App\Core;

abstract class Controller {
    protected function render($view, $data = []) {
        $router = new Router();
        return $router->renderView($view, $data);
    }

    protected function redirect($url) {
        header("Location: $url");
        exit;
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
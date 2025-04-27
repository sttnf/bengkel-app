<?php

namespace App\Core;

class Router
{
    private $routes = [];

    public function get($path, $callback)
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback)
    {
        $this->addRoute('POST', $path, $callback);
    }

    private function addRoute($method, $path, $callback)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }

    public function resolve()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri, $params)) {
                return call_user_func_array($route['callback'], $params);
            }
        }

        http_response_code(404);
        return $this->renderView('404');
    }

    private function matchPath($routePath, $uri, &$params = [])
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($uri, '/'));

        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        $params = [];

        foreach ($routeParts as $index => $routePart) {
            if (strpos($routePart, ':') === 0) {
                $params[] = $uriParts[$index];
            } elseif ($routePart !== $uriParts[$index]) {
                return false;
            }
        }

        return true;
    }

    public function renderView($view, $data = [])
    {
        extract($data);

        $layoutContent = $this->layoutContent();
        $viewContent = $this->viewContent($view, $data);

        if ($viewContent === false) {
            http_response_code(500);
            return 'Internal Server Error';
        }

        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    private function layoutContent()
    {
        ob_start();
        include_once __DIR__ . '/../Views/layouts/main.php';
        return ob_get_clean();
    }

    private function viewContent($view, $data)
    {
        $viewPath = __DIR__ . "/../Views/$view.php";
        if (!file_exists($viewPath)) {
            $viewPath = __DIR__ . '/../Views/404.php';
        }
        extract($data);
        ob_start();
        include_once $viewPath;
        return ob_get_clean();
    }
}

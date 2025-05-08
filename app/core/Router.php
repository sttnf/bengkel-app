<?php

namespace App\Core;

use Exception;

class Router
{
    protected array $routes = [];
    protected string $groupPrefix = '';

    public function get(string $path, $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post(string $path, $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    public function group(string $prefix, callable $callback): void
    {
        $previousPrefix = $this->groupPrefix;
        $this->groupPrefix = rtrim("$previousPrefix/" . trim($prefix, '/'), '/');
        $callback($this);
        $this->groupPrefix = $previousPrefix;
    }

    protected function addRoute(string $method, string $path, $callback): void
    {
        $fullPath = '/' . trim("$this->groupPrefix/" . trim($path, '/'), '/');
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $fullPath ?: '/',
            'callback' => $callback,
        ];
    }

    /**
     * @throws Exception
     */
    public function resolve(): mixed
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/', '/') ?: '/';

        foreach ($this->routes as $route) {
            $params = [];
            if ($route['method'] === strtoupper($method) && $this->matchRoute($route['path'], $uri, $params)) {
                return $this->executeCallback($route['callback'], $params);
            }
        }

        return $this->handleNotFound();
    }

    protected function matchRoute(string $routePath, string $uri, array &$params = []): bool
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($uri, '/'));

        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        $params = [];
        foreach ($routeParts as $i => $routePart) {
            if (str_starts_with($routePart, ':')) {
                $params[substr($routePart, 1)] = $uriParts[$i];
            } elseif ($routePart !== $uriParts[$i]) {
                return false;
            }
        }

        return true;
    }

    protected function executeCallback($callback, array $params = []): mixed
    {
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }
        if (is_array($callback)) {
            if (is_string($callback[0]) && class_exists($callback[0])) {
                // If the first element is a string (class name)
                return call_user_func_array([new $callback[0](), $callback[1]], $params);
            } elseif (is_object($callback[0])) {
                // If the first element is already an object instance
                return call_user_func_array([$callback[0], $callback[1]], $params);
            }
        }

        if (is_string($callback) && str_contains($callback, '@')) {
            [$controllerClass, $method] = explode('@', $callback);
            if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
                return call_user_func_array([new $controllerClass, $method], $params);
            }
        }

        throw new Exception("Invalid route callback: " . print_r($callback, true));
    }

    protected function handleNotFound(): string
    {
        http_response_code(404);
        return $this->renderView('404');
    }

    public function renderView(string $view, array $data = [], string $layout = 'main'): string
    {
        extract($data);
        $layoutContent = $this->layoutContent($layout);
        $viewContent = $this->viewContent($view, $data);

        if ($viewContent === false) {
            throw new Exception("View not found: " . htmlspecialchars($view));
        }

        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    public function renderJson(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function layoutContent(string $layout): string
    {
        ob_start();
        include_once __DIR__ . "/../Views/layouts/$layout.php";
        return ob_get_clean();
    }

    protected function viewContent(string $view, array $data): string|bool
    {
        $viewPath = __DIR__ . "/../Views/$view.php";
        if (!file_exists($viewPath)) {
            return false;
        }

        extract($data);
        ob_start();
        include_once $viewPath;
        return ob_get_clean();
    }

    public function serveStaticFile(string $filePath): void
    {
        $publicDir = dirname(__DIR__) . '/../public';
        $fullPath = realpath("$publicDir/" . ltrim($filePath, '/'));

        if ($fullPath && is_file($fullPath) && is_readable($fullPath)) {
            header('Content-Type: ' . mime_content_type($fullPath));
            readfile($fullPath);
            exit;
        }

        http_response_code(404);
        echo "Static file not found: " . htmlspecialchars($filePath);
    }
}
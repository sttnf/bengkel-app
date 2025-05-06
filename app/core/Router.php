<?php

namespace App\Core;

use Exception;

class Router
{
    /**
     * Stores the defined routes.
     *
     * @var array
     */
    protected array $routes = [];

    /**
     * Adds a new route for the GET method.
     *
     * @param string $path The URI path.
     * @param callable|array|string $callback The action to execute.
     * @return void
     */
    public function get(string $path, $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * Adds a new route for the POST method.
     *
     * @param string $path The URI path.
     * @param callable|array|string $callback The action to execute.
     * @return void
     */
    public function post(string $path, $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * Adds a new route to the routes array.
     *
     * @param string $method The HTTP method.
     * @param string $path The URI path.
     * @param callable|array|string $callback The action to execute.
     * @return void
     */
    protected function addRoute(string $method, string $path, $callback): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'callback' => $callback,
        ];
    }

    /**
     * Matches the current request URI against the defined routes and executes the callback.
     *
     * @return mixed The result of the route callback or a 404 response.
     */
    public function resolve(): mixed
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchRoute($route['path'], $uri, $params)) {
                return $this->executeCallback($route['callback'], $params);
            }
        }

        return $this->handleNotFound();
    }

    /**
     * Matches a route path with the current URI and extracts parameters.
     *
     * @param string $routePath The defined route path.
     * @param string $uri The current URI.
     * @param array $params An array to store extracted parameters (passed by reference).
     * @return bool True if the route matches, false otherwise.
     */
    protected function matchRoute(string $routePath, string $uri, &$params = []): bool
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($uri, '/'));

        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        $params = [];
        foreach ($routeParts as $index => $routePart) {
            if (str_starts_with($routePart, ':')) {
                $paramName = substr($routePart, 1);
                $params[$paramName] = $uriParts[$index];
            } elseif ($routePart !== $uriParts[$index]) {
                return false;
            }
        }
        return true;
    }

    /**
     * Executes the callback function or method for a matched route.
     *
     * @param callable|array|string $callback The function, method array, or controller@method string.
     * @param array $params An array of parameters to pass to the callback.
     * @return mixed The result of the callback execution.
     */
    protected function executeCallback($callback, array $params = []): mixed
    {
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        } elseif (is_array($callback) && is_string($callback[0]) && method_exists($callback[0], $callback[1])) {
            $controller = new $callback[0]();
            return call_user_func_array([$controller, $callback[1]], $params);
        } elseif (is_string($callback) && strpos($callback, '@') !== false) {
            [$controllerClass, $method] = explode('@', $callback);
            if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
                $controller = new $controllerClass();
                return call_user_func_array([$controller, $method], $params);
            }
        }

        throw new Exception("Invalid route callback: " . print_r($callback, true));
    }

    /**
     * Handles the case where no matching route is found.
     *
     * @return string The content of the 404 view.
     */
    protected function handleNotFound(): string
    {
        http_response_code(404);
        return $this->renderView('404');
    }

    /**
     * Renders a view file with optional data.
     *
     * @param string $view The name of the view file.
     * @param array $data An associative array of data to pass to the view.
     * @return string The rendered HTML content.
     */
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

    /**
     * Includes and returns the content of the layout file.
     *
     * @return string The layout content.
     */
    protected function layoutContent(string $layout = 'main'): string
    {
        ob_start();
        include_once __DIR__ . "/../Views/layouts/$layout.php";
        return ob_get_clean();
    }

    /**
     * Includes and returns the content of a specific view file.
     *
     * @param string $view The name of the view file.
     * @param array $data An associative array of data to pass to the view.
     * @return string|bool The view content or false if the file doesn't exist.
     */
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

    /**
     * Serves static files from the public directory.
     *
     * @param string $filePath The path to the static file relative to the public directory.
     * @return void
     */
    public function serveStaticFile(string $filePath): void
    {
        $publicDir = dirname(__DIR__) . '/../public';
        $fullPath = realpath($publicDir . '/' . ltrim($filePath, '/'));


        if ($fullPath && is_file($fullPath) && is_readable($fullPath)) {
            $mimeType = mime_content_type($fullPath);
            header('Content-Type: ' . $mimeType);
            readfile($fullPath);
            exit; // Important: Stop further script execution after serving the file
        }

        http_response_code(404);
        echo "Static file not found: " . htmlspecialchars($filePath);
    }
}
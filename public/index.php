<?php
require_once __DIR__ . '/../vendor/autoload.php';

$router = new \App\Core\Router();

require_once __DIR__ . '/../routes/web.php';

echo $router->resolve();
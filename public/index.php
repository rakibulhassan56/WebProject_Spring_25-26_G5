<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

$route = (string) ($_GET['route'] ?? 'home');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

(new Router())->dispatch($route, $method);

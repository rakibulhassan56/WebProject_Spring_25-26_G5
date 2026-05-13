<?php

declare(strict_types=1);

function base_path(string $path = ''): string
{
    return dirname(__DIR__) . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
}

function url(string $route, array $query = []): string
{
    $query = array_merge(['route' => $route], $query);
    return '/index.php?' . http_build_query($query);
}

function redirect(string $route, array $query = []): void
{
    header('Location: ' . url($route, $query));
    exit;
}

function json_response(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_THROW_ON_ERROR);
    exit;
}

function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        redirect('auth/login');
    }
}

/**
 * @param list<string> $roles
 */
function require_role(array $roles): void
{
    require_login();
    $role = $_SESSION['role'] ?? '';
    if (!in_array($role, $roles, true)) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

function view(string $name, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $file = base_path('views/' . $name . '.php');
    if (!is_file($file)) {
        http_response_code(500);
        echo 'View not found';
        exit;
    }
    require $file;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function verify_csrf(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['_csrf'])
        && hash_equals($_SESSION['_csrf'], $token);
}

/**
 * @param list<string> $roles
 */
function require_api_role(array $roles): void
{
    if (empty($_SESSION['user_id'])) {
        json_response(['ok' => false, 'error' => 'unauthorized'], 401);
    }
    $role = $_SESSION['role'] ?? '';
    if (!in_array($role, $roles, true)) {
        json_response(['ok' => false, 'error' => 'forbidden'], 403);
    }
}

function read_json_body(): array
{
    $raw = file_get_contents('php://input') ?: '';
    if ($raw === '') {
        return [];
    }
    try {
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        return is_array($decoded) ? $decoded : [];
    } catch (Throwable) {
        return [];
    }
}

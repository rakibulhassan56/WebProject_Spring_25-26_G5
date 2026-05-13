<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';

spl_autoload_register(static function (string $class): void {
    foreach ([__DIR__ . '/../models/', __DIR__ . '/../controllers/'] as $dir) {
        $file = $dir . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

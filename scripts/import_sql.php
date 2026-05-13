<?php

declare(strict_types=1);

$base = dirname(__DIR__);
$pdo = new PDO(
    'mysql:host=127.0.0.1;dbname=assessment;charset=utf8mb4',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
foreach (['schema.sql', 'seed.sql'] as $f) {
    $sql = file_get_contents($base . '/sql/' . $f);
    if ($sql === false) {
        throw new RuntimeException('Missing ' . $f);
    }
    foreach (preg_split("/;\s*\n/", $sql) as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '' || str_starts_with($stmt, '--')) {
            continue;
        }
        $pdo->exec($stmt);
    }
    echo "Imported {$f}\n";
}

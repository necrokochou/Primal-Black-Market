<?php
require_once '/bootstrap.php';

function connectPostgres(): PDO {
    $env = getPostgresEnv();
    $dsn = "pgsql:host={$env['host']};port={$env['port']};dbname={$env['db']}";
    return new PDO($dsn, $env['user'], $env['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
}
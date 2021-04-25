<?php

use app\core\Application;
use app\controllers\SiteController;
use app\controllers\AuthController;
use app\core\Router;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$config = [
    /*
     * To powinno działać normalnie ale w kontenerze dockera trzeba się łączyć inaczej
     *
    'db' => [
        'dsn'         => $_ENV['DB_DSN'],
        'user'        => $_ENV['DB_USER'],
        'password'    => $_ENV['DB_PASSWORD']
    ]
    */
    'db' => [
        'dsn'         => 'mysql:host=localhost:3307;dbname=sf',
        'user'        => $_ENV['DB_USER'],
        'password'    => $_ENV['DB_PASSWORD']
    ]
];

$app = new Application(__DIR__, $config);

$app->db->applyMigrations();



<?php

use app\core\Application;
use app\controllers\SiteController;
use app\controllers\AuthController;
use app\core\Router;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$config = [
  'db' => [
      'dsn'      => $_ENV['DB_DSN'],
      'user'     => $_ENV['DB_USER'],
      'password' => $_ENV['DB_PASSWORD']
  ]
];

$app = new Application(dirname(__DIR__), $config);

$router = new Router($app->request, $app->response);

$app->router->get('/', 'home');
$app->router->get('/contact', 'contact');
$app->router->post('/contact', [SiteController::class, 'handleContact']);

$app->router->get('/login', [AuthController::class, 'login']);
$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/login', [AuthController::class, 'login']);
$app->router->post('/register', [AuthController::class, 'register']);

$app->run();


<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\core\Application;
use app\controllers\SiteController;
use app\controllers\AuthController;
use app\core\Router;

/**
 * Entry script
 */

$app = new Application(dirname(__DIR__));

$router = new Router($app->request, $app->response);

//$app->userRouter($router);

$app->router->get('/', 'home');
$app->router->get('/contact', 'contact');
$app->router->post('/contact', [SiteController::class, 'handleContact']);

$app->router->get('/login', [AuthController::class, 'login']);
$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/login', [AuthController::class, 'login']);
$app->router->post('/register', [AuthController::class, 'register']);

$app->run();

// https://www.youtube.com/watch?v=6ERdu4k62wI&t=6371s 2:21:48


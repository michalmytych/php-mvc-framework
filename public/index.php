<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\core\Application;
use app\core\Router;

/**
 * Entry script
 */

$app = new Application(dirname(__DIR__));

$router = new Router($app->request, $app->response);

//$app->userRouter($router);

$app->router->get('/', 'home');
$app->router->get('/contact', 'contact');

$app->run();

// https://www.youtube.com/watch?v=6ERdu4k62wI&t=6371s 38:01


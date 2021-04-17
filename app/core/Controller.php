<?php

namespace app\core;

/**
 * Base Class Controller for
 * custom app controllers
 * @package app\core
 */

class Controller
{
    public string $layout = 'main';

    public function setLayout(string $layout) : void
    {
        $this->layout = $layout;
    }

    public function render($view, $params = [])
    {
        return Application::$app->router->renderView($view, $params);
    }
}
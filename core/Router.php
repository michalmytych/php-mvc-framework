<?php

namespace app\core;

/**
 * Class Router
 * @package app\core
 */

class Router
{
    public Request $request;

    public Response $response;

    protected array $routes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, string $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post(string $path, string $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            $this->response->setStatusCode(404);
            return "Not found";
        }

        if (is_string($callback)) {
            return $this->renderView($callback);
        }

        echo call_user_func($callback);

        var_dump($this->request->getPath());
    }

    public function renderView(string $view)
    {
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderOnlyView($view);
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    protected function layoutContent()
    {
        ob_start();
        include_once Application::$ROOT_DIR . "/views/layouts/main.php";
        return ob_get_clean();
    }

    protected function renderOnlyView(string $view)
    {
        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view/main.php";
        return ob_get_clean();
    }
}

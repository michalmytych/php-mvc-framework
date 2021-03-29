<?php


namespace app\core;

/**
 * Class Request
 * @package app\core
 */

class Request
{
    public function getPath() : string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos(
            $path, '?'
        );

        if ($position === false) {
            // if no question mark in URI
            return $path;
        }

        return substr($path, 0, $position);
    }

    public function getMethod() : string
    {
        // or rise some Exception
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
}

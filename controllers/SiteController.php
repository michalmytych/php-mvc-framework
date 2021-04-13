<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;

/**
 * Class SiteController
 * @package app\controllers
 */

class SiteController extends Controller
{
    public function home()
    {
        $params = [
            'name' => "HttpMike"
        ];
        return $this->render('home', $params);
    }

    public function contact()
    {
        return $this->render('contact');
    }

    public function handleContact(Request $request)
    {
        $body = $request->getBody();
        return var_dump($body);
    }
}
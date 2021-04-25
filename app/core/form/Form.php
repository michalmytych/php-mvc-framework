<?php

namespace app\core\form;

use app\core\form\Field;
use app\core\Model;

class Form
{
    public static function begin(string $action, string $method) : object
    {
        echo sprintf('<form action="%s" method="%s">', $action, $method);
        return new Form();
    }

    public static function end()
    {
        echo '</form>';
    }

    public function field(Model $model, string $attribute) : Field
    {
        return new Field($model, $attribute);
    }
}
<?php

namespace app\core;


abstract class DBModel extends Model
{
    public function save() : bool
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $preparedStmt = self::prepare(
            "INSERT INTO $tableName (" . implode(', ', $attributes) . ") 
            VALUES(" . implode(', ', $params) . ")"
        );

        foreach ($attributes as $attribute) {
            $preparedStmt->bindValue(":$attribute", $this->{$attribute});
        }

        /**
         * @todo - add try catch block for preparedStmt execution (print db errors)
         */
        $preparedStmt->execute();

        return true;
    }

    abstract function attributes() : array;

    public static function prepare(string $sqlString) : \PDOStatement
    {
        return Application::$app->db->pdo->prepare($sqlString);
    }
}

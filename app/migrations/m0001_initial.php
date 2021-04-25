<?php

class m0001_initial
{
    public function up()
    {
        $db = \app\core\Application::$app->db;

        $sqlStmt = "create table users (
            id int auto_increment PRIMARY KEY,
            email varchar(255) not null,
            firstname varchar(255) not null,
            lastname varchar(255) not null,
            status tinyint null,
            created_at timestamp default current_timestamp            
        ) engine=INNODB;";

        $db->pdo->exec($sqlStmt);
    }

    public function down()
    {
        $db = \app\core\Application::$app->db;

        $sqlStmt = $db->pdo->prepare("DROP table users");
        $db->pdo->exec($sqlStmt);
    }
}

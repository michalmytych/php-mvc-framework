<?php

namespace app\models;

use app\core\DBModel;

class User extends DBModel
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    public string $firstname = '';
    public string $lastname = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirm = '';
    public int $status = self::STATUS_INACTIVE;

    public function rules() : array
    {
        return [
            'firstname'         => [self::RULE_REQUIRED],
            'lastname'          => [self::RULE_REQUIRED],
            'password'          => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8]],
            'passwordConfirm'   => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']],
            'email'             => [self::RULE_REQUIRED, self::RULE_EMAIL,
                [self::RULE_UNIQUE, 'class' => self::class, 'attribute' => 'email']]
        ];
    }

    public function save() : bool
    {
        $this->status = self::STATUS_INACTIVE;
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        return parent::save();
    }

    public function tableName() : string
    {
        return 'users';
    }

    public function attributes() : array
    {
        return [
            'firstname',
            'lastname',
            'email',
            'status',
            'password'
        ];
    }

    public function labels() : array
    {
        return [
            'firstname'       => 'First name',
            'lastname'        => 'Last name',
            'email'           => 'Email',
            'password'        => 'Password',
            'passwordConfirm' => 'Confirm password',
        ];
    }
}

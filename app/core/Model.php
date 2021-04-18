<?php

namespace app\core;

abstract class Model
{
    public array $errors = array();
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';    // match password to passwordConfirm
    public const RULE_UNIQUE = 'unique';
    // @todo - implement 'UNIQUE' rule

    abstract public function rules() : array;

    public function loadData(array $data)
    {
        foreach($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function validate() : bool
    {
        foreach($this->rules() as $attr => $rules) {
            $value = $this->{$attr};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                /**
                 * @todo - pewnie warto by rozbić walidacje (if-y) na osobne funkcje lub przenieść do osobnej klasy
                 */
                if (! is_string($ruleName)) {
                    $ruleName = $rule[0];
                }
                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addError($attr, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($attr, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addError($attr, self::RULE_MIN, $rule);
                }
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addError($attr, self::RULE_MAX, $rule);
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $this->addError($attr, self::RULE_MATCH, $rule);
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attr;
                    $tableName = $className::tableName();

                    $stmt = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                    $stmt->bindValue(":attr", $value);
                    $stmt->execute();
                    $record = $stmt->fetchObject();
                    if ($record) {
                        $this->addError($attr, self::RULE_UNIQUE, ['field' => $attr]);
                    }
                }

            }
        }
        return empty($this->errors);
    }

    private function addError($attribute, string $rule, array $params = []) : void
    {
        $message = $this->errorMessages()[$rule] ?? '';
        foreach($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    public function errorMessages() : array
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL    => 'This field must be valid email address',
            self::RULE_MIN      => 'Minimal length of this field must be {min}',
            self::RULE_MAX      => 'Maximal length of this field must be {max}',
            self::RULE_MATCH    => 'This field`s value must be same as "{match}" field value',
            self::RULE_UNIQUE   => 'Record with this {field} already exists'
        ];
    }

    public function hasErrors(string $attribute) : bool
    {
        return empty($this->errors[$attribute]) ?? false;
    }

    public function getFirstError(string $attribute)
    {
        //var_dump($this->errors[$attribute][0]); exit();
        return $this->errors[$attribute][0] ?? false;
    }
}
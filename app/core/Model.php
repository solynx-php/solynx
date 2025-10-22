<?php

namespace app\core;

abstract class Model
{
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';
    public const RULE_REGEX = 'regex';
    public const RULE_IN = 'in';
    public const RULE_NOT_IN = 'not_in';
    public const RULE_DATE = 'date';
    public const RULE_NUMERIC = 'numeric';
    public const RULE_INTEGER = 'integer';
    public const RULE_FLOAT = 'float';
    public const RULE_BOOLEAN = 'boolean';
    public const RULE_ARRAY = 'array';
    public const RULE_FILE = 'file';
    public const RULE_IMAGE = 'image';
    public const RULE_URL = 'url';
    public const RULE_LENGTH = 'length';
    public $errors = [];


    public function __construct() {}

    public function all($request)
    {
        foreach ($request as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function rules()
    {
        return [];
    }

    public function labels()
    {
        return [];
    }

    public function getLabel($attribute)
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    public function validate()
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach ($rules as $rule) {
                if ($rule === self::RULE_REQUIRED && !$value) {
                    $this->errorMessages($attribute, self::RULE_REQUIRED);
                }
                if ($rule === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errorMessages($attribute, self::RULE_EMAIL);
                }
                if (is_array($rule) && $rule[0] === self::RULE_MIN) {
                    $minLength = $rule[1];
                    if (strlen($value) < $minLength) {
                        $this->errorMessages($attribute, self::RULE_MIN, ['min' => $minLength]);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_MAX) {
                    $maxLength = $rule[1];
                    if (strlen($value) > $maxLength) {
                        $this->errorMessages($attribute, self::RULE_MAX, ['max' => $maxLength]);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_MATCH) {
                    $matchAttribute = $rule[1];
                    if ($value !== $this->{$matchAttribute}) {
                        $this->errorMessages($attribute, self::RULE_MATCH, ['match' => $this->getLabel($matchAttribute)]);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_UNIQUE) {
                    $className = $rule[1];
                    $uniqueAttribute = $rule[2] ?? $attribute;
                    $tableName = $className::table;
                    $existingRecord = Application::$app->db->findOne($tableName, [$uniqueAttribute => $value]);
                    if ($existingRecord) {
                        $this->errorMessages($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute)]);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_REGEX) {
                    $pattern = $rule[1];
                    if (!preg_match($pattern, $value)) {
                        $this->errorMessages($attribute, self::RULE_REGEX);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_IN) {
                    $allowedValues = $rule[1];
                    if (!in_array($value, $allowedValues)) {
                        $this->errorMessages($attribute, self::RULE_IN);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_NOT_IN) {
                    $disallowedValues = $rule[1];
                    if (in_array($value, $disallowedValues)) {
                        $this->errorMessages($attribute, self::RULE_NOT_IN);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_DATE) {
                    $format = $rule[1];
                    $d = \DateTime::createFromFormat($format, $value);
                    if (!($d && $d->format($format) === $value)) {
                        $this->errorMessages($attribute, self::RULE_DATE);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_NUMERIC) {
                    if (!is_numeric($value)) {
                        $this->errorMessages($attribute, self::RULE_NUMERIC);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_INTEGER) {
                    if (!filter_var($value, FILTER_VALIDATE_INT)) {
                        $this->errorMessages($attribute, self::RULE_INTEGER);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_FLOAT) {
                    if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
                        $this->errorMessages($attribute, self::RULE_FLOAT);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_BOOLEAN) {
                    if (!is_bool($value)) {
                        $this->errorMessages($attribute, self::RULE_BOOLEAN);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_ARRAY) {
                    if (!is_array($value)) {
                        $this->errorMessages($attribute, self::RULE_ARRAY);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_FILE) {
                    if (!isset($_FILES[$attribute]) || $_FILES[$attribute]['error'] !== UPLOAD_ERR_OK) {
                        $this->errorMessages($attribute, self::RULE_FILE);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_IMAGE) {
                    if (!isset($_FILES[$attribute]) || $_FILES[$attribute]['error'] !== UPLOAD_ERR_OK) {
                        $this->errorMessages($attribute, self::RULE_IMAGE);
                    } else {
                        $fileInfo = getimagesize($_FILES[$attribute]['tmp_name']);
                        if ($fileInfo === false) {
                            $this->errorMessages($attribute, self::RULE_IMAGE);
                        }
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_URL) {
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $this->errorMessages($attribute, self::RULE_URL);
                    }
                }
                if (is_array($rule) && $rule[0] === self::RULE_LENGTH) {
                    $length = strlen($value);
                    $min = $rule[1] ?? null;
                    $max = $rule[2] ?? null;
                    if ($min !== null && $length < $min) {
                        $this->errorMessages($attribute, self::RULE_LENGTH);
                    }
                    if ($max !== null && $length > $max) {
                        $this->errorMessages($attribute, self::RULE_LENGTH);
                    }
                }
            }
        }
        return empty($this->errors);
    }

    public function errorMessages($attribute, $rule, $params = [])
    {
        $messages = $this->message()[$rule] ?? '';
        $label = $this->getLabel($attribute);

        $params = array_merge(['field' => $label], $params);

        foreach ($params as $key => $value) {
            $messages = str_replace('{' . $key . '}', $value, $messages);
        }

        $this->errors[$attribute][] = $messages;
    }


    public function message()
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be a valid email address',
            self::RULE_MIN => 'Minimum length of this field must be {min}',
            self::RULE_MAX => 'Maximum length of this field must be {max}',
            self::RULE_MATCH => 'This field must match {match}',
            self::RULE_UNIQUE => 'Record with this {field} already exists',
            self::RULE_REGEX => 'This field format is invalid',
            self::RULE_IN => 'This field must be one of: {values}',
            self::RULE_NOT_IN => 'This field contains a disallowed value',
            self::RULE_DATE => 'This field must be a valid date in the format {format}',
            self::RULE_NUMERIC => 'This field must be a numeric value',
            self::RULE_INTEGER => 'This field must be an integer',
            self::RULE_FLOAT => 'This field must be a float',
            self::RULE_BOOLEAN => 'This field must be a boolean',
            self::RULE_ARRAY => 'This field must be an array',
            self::RULE_FILE => 'This field must be a valid uploaded file',
            self::RULE_IMAGE => 'This field must be a valid uploaded image file',
            self::RULE_URL => 'This field must be a valid URL',
            self::RULE_LENGTH => 'This field length must be between {min} and {max}'
        ];
    }

    public function hasError($attribute)
    {
        return isset($this->errors[$attribute]);
    }
    public function getError($attribute)
    {
        return $this->errors[$attribute][0] ?? '';
    }
}

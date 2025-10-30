<?php

namespace app\core;

use app\core\database\DB;

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
    protected array $attributes = [];

    public array $errors = [];

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }


    // Populate model properties from a request
    public function fill(array $data): static
    {
        foreach ($data as $key => $value) {
            if (!empty($this->fillable) && !in_array($key, $this->fillable, true)) {
                continue;
            }
            $this->{$key} = $value;
        }
        return $this;
    }


    public function rules()
    {
        return [];
    }

    public function labels()
    {
        return [];
    }

    public function getLabel(string $attribute)
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    // ----------------------------
    // VALIDATION LOGIC
    // ----------------------------
    public function validate()
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute} ?? null;

            foreach ($rules as $rule) {
                // Required
                if ($rule === self::RULE_REQUIRED && ($value === null || $value === '')) {
                    $this->errorMessages($attribute, self::RULE_REQUIRED);
                    continue;
                }

                // Email
                if ($rule === self::RULE_EMAIL && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errorMessages($attribute, self::RULE_EMAIL);
                }

                // Min length
                if (is_array($rule) && $rule[0] === self::RULE_MIN) {
                    $minLength = (int) $rule[1];
                    if (strlen((string)($value ?? '')) < $minLength) {
                        $this->errorMessages($attribute, self::RULE_MIN, ['min' => $minLength]);
                    }
                }

                // Max length
                if (is_array($rule) && $rule[0] === self::RULE_MAX) {
                    $maxLength = (int) $rule[1];
                    if (strlen((string)($value ?? '')) > $maxLength) {
                        $this->errorMessages($attribute, self::RULE_MAX, ['max' => $maxLength]);
                    }
                }

                // Match
                if (is_array($rule) && $rule[0] === self::RULE_MATCH) {
                    $matchAttribute = $rule[1];
                    if ($value !== ($this->{$matchAttribute} ?? null)) {
                        $this->errorMessages($attribute, self::RULE_MATCH, [
                            'match' => $this->getLabel($matchAttribute)
                        ]);
                    }
                }

                // Unique
                if (is_array($rule) && $rule[0] === self::RULE_UNIQUE) {
                    $className = $rule[1];
                    $uniqueAttribute = $rule[2] ?? $attribute;
                    $tableName = $className::table();
                    $pdo = DB::pdo();
                    $stmt = $pdo->prepare("SELECT 1 FROM `$tableName` WHERE `$uniqueAttribute` = ? LIMIT 1");
                    $stmt->execute([$value]);
                    if ($stmt->fetchColumn()) {
                        $this->errorMessages($attribute, self::RULE_UNIQUE, [
                            'field' => $this->getLabel($attribute)
                        ]);
                    }
                }

                // Regex
                if (is_array($rule) && $rule[0] === self::RULE_REGEX && $value) {
                    $pattern = $rule[1];
                    if (!preg_match($pattern, (string) $value)) {
                        $this->errorMessages($attribute, self::RULE_REGEX);
                    }
                }

                // In / Not in
                if (is_array($rule) && $rule[0] === self::RULE_IN && $value !== null) {
                    $allowed = $rule[1];
                    if (!in_array($value, $allowed, true)) {
                        $this->errorMessages($attribute, self::RULE_IN);
                    }
                }

                if (is_array($rule) && $rule[0] === self::RULE_NOT_IN && $value !== null) {
                    $disallowed = $rule[1];
                    if (in_array($value, $disallowed, true)) {
                        $this->errorMessages($attribute, self::RULE_NOT_IN);
                    }
                }

                // Date
                if (is_array($rule) && $rule[0] === self::RULE_DATE && $value) {
                    $format = $rule[1];
                    $d = \DateTime::createFromFormat($format, $value);
                    if (!($d && $d->format($format) === $value)) {
                        $this->errorMessages($attribute, self::RULE_DATE);
                    }
                }

                // Numeric / Integer / Float / Boolean / Array
                if (is_array($rule) && $rule[0] === self::RULE_NUMERIC && $value !== null && !is_numeric($value)) {
                    $this->errorMessages($attribute, self::RULE_NUMERIC);
                }

                if (is_array($rule) && $rule[0] === self::RULE_INTEGER && $value !== null && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->errorMessages($attribute, self::RULE_INTEGER);
                }

                if (is_array($rule) && $rule[0] === self::RULE_FLOAT && $value !== null && !filter_var($value, FILTER_VALIDATE_FLOAT)) {
                    $this->errorMessages($attribute, self::RULE_FLOAT);
                }

                if (is_array($rule) && $rule[0] === self::RULE_BOOLEAN && $value !== null && !is_bool($value)) {
                    $this->errorMessages($attribute, self::RULE_BOOLEAN);
                }

                if (is_array($rule) && $rule[0] === self::RULE_ARRAY && $value !== null && !is_array($value)) {
                    $this->errorMessages($attribute, self::RULE_ARRAY);
                }

                // File / Image
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

                // URL
                if (is_array($rule) && $rule[0] === self::RULE_URL && $value) {
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $this->errorMessages($attribute, self::RULE_URL);
                    }
                }

                // Length
                if (is_array($rule) && $rule[0] === self::RULE_LENGTH) {
                    $length = strlen((string)($value ?? ''));
                    $min = $rule[1] ?? null;
                    $max = $rule[2] ?? null;
                    if ($min !== null && $length < $min) {
                        $this->errorMessages($attribute, self::RULE_LENGTH, ['min' => $min]);
                    }
                    if ($max !== null && $length > $max) {
                        $this->errorMessages($attribute, self::RULE_LENGTH, ['max' => $max]);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    // ----------------------------
    // ERROR HANDLING
    // ----------------------------
    public function errorMessages(string $attribute, string $rule, array $params = [])
    {
        $message = $this->message()[$rule] ?? '';
        $label = $this->getLabel($attribute);
        $params = array_merge(['field' => $label], $params);

        foreach ($params as $key => $value) {
            $message = str_replace('{' . $key . '}', (string)$value, $message);
        }

        $this->errors[$attribute][] = $message;
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

    // ----------------------------
    // ERROR HELPERS
    // ----------------------------
    public function hasError(string $attribute)
    {
        return isset($this->errors[$attribute]);
    }

    public function getError(string $attribute)
    {
        return $this->errors[$attribute][0] ?? '';
    }
}

<?php

namespace app\models;

class UserModel extends Model
{
    protected array $fillable = ['username', 'email', 'password', 'confirmpassword'];
    protected array $hidden = ['password'];

    public static function table(): string
    {
        return 'users';
    }

    public function rules(): array
    {
        return [
            'username'        => [self::RULE_REQUIRED, [self::RULE_MIN, 3], [self::RULE_MAX, 50]],
            'email'           => [self::RULE_REQUIRED, self::RULE_EMAIL, [self::RULE_MAX, 255], [self::RULE_UNIQUE, self::class, 'email']],
            'password'        => [self::RULE_REQUIRED, [self::RULE_MIN, 6]],
            'confirmpassword' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'password']],
        ];
    }

    public function labels(): array
    {
        return [
            'username'        => 'Username',
            'email'           => 'Email Address',
            'password'        => 'Password',
            'confirmpassword' => 'Confirm Password',
        ];
    }

    public function message(): array
    {
        return [
            self::RULE_REQUIRED => 'The {field} field is required.',
            self::RULE_EMAIL    => 'The {field} must be a valid email address.',
            self::RULE_MIN      => 'The {field} must be at least {min} characters long.',
            self::RULE_MAX      => 'The {field} must not exceed {max} characters.',
            self::RULE_MATCH    => 'The {field} must match {match}.',
            self::RULE_UNIQUE   => 'The {field} has already been taken.',
        ];
    }

    public function save(): bool
    {
        // Validate before hashing password
        if (!$this->validate()) {
            return false;
        }

        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        // You can insert here or delegate to ActiveRecord later
        return true;
    }
}

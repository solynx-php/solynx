<?php

namespace app\models;

class UserModel extends Model
{
    protected array $fillable = ['username', 'email', 'password'];
    protected array $hidden = ['password'];
    protected static string $table = 'users';

    public function rules()
    {
        return [
            'username'        => [
                self::RULE_REQUIRED,
                [self::RULE_UNIQUE, self::class, 'username'],
                [self::RULE_MIN, 3],
                [self::RULE_MAX, 50]
            ],
            'email'           => [self::RULE_REQUIRED, self::RULE_EMAIL, [self::RULE_MAX, 255], [self::RULE_UNIQUE, self::class, 'email']],
            'password'        => [self::RULE_REQUIRED, [self::RULE_MIN, 6]],
        ];
    }

    public function labels()
    {
        return [
            'username'        => 'Username',
            'email'           => 'Email Address',
            'password'        => 'Password',
        ];
    }

    public function message()
    {
        return [
            self::RULE_REQUIRED => 'The {field} field is required.',
            self::RULE_EMAIL    => 'The {field} must be a valid email address.',
            self::RULE_MIN      => 'The {field} must be at least {min} characters long.',
            self::RULE_MAX      => 'The {field} must not exceed {max} characters.',
            self::RULE_UNIQUE   => 'The {field} has already been taken.',
            self::RULE_MATCH    => 'The {field} not matched.'
        ];
    }

    // public function save()
    // {
    //     if (!$this->validate()) {
    //         return false;
    //     }

    //     $this->password = password_hash($this->password, PASSWORD_DEFAULT);

    //     return parent::save();
    // }
}

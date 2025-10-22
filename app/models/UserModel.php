<?php

namespace app\models;


class UserModel extends Model
{
    public $table = 'users'; 
    public $username = '';
    public $email = '';
    public $password = '';
    public $confirmpassword = '';
    
    public function tableName()
    {
        return $this->table;
    }

    public function rules()
    {
        return [
            'username' => ["required", ["min",3], ["max",50]],
            'email' => ["required", "email", ["max", 255]],
            'password' => ["required", ["min",6]],
            'confirmpassword' => ["required", ["match","password"]],
        ];
    }

    public function labels()
    {
        return [
            'username' => 'Username',
            'email' => 'Email Address',
            'password' => 'Password',
            'confirmpassword' => 'Confirm Password',
        ];
    }

    public function message()
    {
        return [
            "required" => 'The {field} field is required you fool.',
            "email" => 'The {field} must be a valid email address.',
            "min" => 'The {field} must be at least {min} characters long.',
            "max" => 'The {field} must not exceed {max} characters.',
            "match" => 'The {field} must match {match}.',
            "unique" => 'The {field} has already been taken.',
        ];
    }

    public function save()
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        return "success";
    }

}

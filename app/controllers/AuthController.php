<?php

namespace app\controllers;

use app\core\log\Logger;
use app\core\Request;
use app\models\UserModel;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if ($request->method() === 'post') {
            $body = $request->body();
            $email = $body['email'] ?? '';
            $password = $body['password'] ?? '';
            $remember = isset($body['remember']) ? true : false;

            // For demonstration purposes, we'll just return the values.
            return $this->render('login', [
                'email' => htmlspecialchars($email),
                'password' => htmlspecialchars($password),
                'remember' => $remember ? 'Yes' : 'No'
            ]);
        }
        return $this->render('login');
    }
    public function register(Request $request)
    {
        $user = new UserModel();
        if ($request->isPost()) {
            $user->all($request->body());

            if ($user->validate()) {
                // Registration successful, redirect or show success message
                return "Registration successful!";
            }

            // Handle registration logic here
            return $this->render('register', [
                'user' => $user
            ]);
        }
        return $this->render('register', [
            'user' => $user
        ]);
    }
    public function registerSubmit($request)
    {
        $body = $request->body();

        // Handle registration logic here
        return "Registration form submitted!";
    }
}

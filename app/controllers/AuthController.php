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
            // $user->fill($request->body());

            // if ($user->validate()) {
            //     $user->create();
            //     return "Registration successful!";
            // }
            $request = $request->body();

            if ($request['password'] != '') {
                $request['password'] = password_hash($request['password'], PASSWORD_DEFAULT);
            }

            $user = UserModel::create($request);

            return $this->render('register', [
                'user' => $user
            ]);
        }
        return $this->render('register', [
            'user' => $user
        ]);
    }
}

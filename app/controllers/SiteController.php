<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\models\UserModel;

class SiteController extends Controller
{
    public function home()
    {
        return "Welcome to the Home Page!";
    }

    public function contact()
    {
        $users = UserModel::all();
        $data = array_map(fn($user) => $user->toArray(), $users);
        return json_encode($data, JSON_PRETTY_PRINT);
    }
    public function handleContactForm(Request $request)
    {
        var_dump($request->body());
        exit;
        // Handle form submission logic here
        return "Contact form submitted!";
    }
}

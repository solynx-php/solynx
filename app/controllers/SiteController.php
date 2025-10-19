<?php
namespace app\controllers;

use app\core\Application;

class SiteController {
    public function home() {
        return "Welcome to the Home Page!";
    }

    public function contact() {
        return Application::$app->router->renderView('contact');
    }
    public function handleContactForm() {
        // Handle form submission logic here
        return "Contact form submitted!";
    }
}
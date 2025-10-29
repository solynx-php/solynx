<?php

namespace app\controllers\Post;
use app\controllers\Controller;
use app\models\Post\PostModel;

class PostController extends Controller
{
    public function index()
    {
        $posts = PostModel::all()->with('users');
        return $posts;
    }
}

<?php

namespace app\controllers\Post;
use app\controllers\Controller;
use app\models\Post\PostModel;
use app\core\Application;

class PostController extends Controller
{
    public function index()
    {
        $posts = PostModel::all()->with('users');
        return $posts;
    }

    public function show($request, $id)
    {
        return ['id' => $id, 'request' => $request->ip()];
    }
}

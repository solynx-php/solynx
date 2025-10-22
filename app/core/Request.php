<?php

namespace app\core;

class Request
{
    public function path()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
    }

    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet()
    {
        return $this->method() === 'get';
    }
    public function isPost()
    {
        return $this->method() === 'post';
    }
    public function isPut()
    {
        return $this->method() === 'put';
    }
    public function isDelete()
    {
        return $this->method() === 'delete';
    }
    public function isPatch()
    {
        return $this->method() === 'patch';
    }
    public function isOptions()
    {
        return $this->method() === 'options';
    }

    public function body()
    {
        $body = [];
        if ($this->method() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->method() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->method() === 'put') {
            parse_str(file_get_contents("php://input"), $put_vars);
            foreach ($put_vars as $key => $value) {
                $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->method() === 'delete') {
            parse_str(file_get_contents("php://input"), $delete_vars);
            foreach ($delete_vars as $key => $value) {
                $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->method() === 'patch') {
            parse_str(file_get_contents("php://input"), $patch_vars);
            foreach ($patch_vars as $key => $value) {
                $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->method() === 'options') {
            parse_str(file_get_contents("php://input"), $options_vars);
            foreach ($options_vars as $key => $value) {
                $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}

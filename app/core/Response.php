<?php
namespace app\core;

class Response
{
    protected int $status = 200;
    protected array $headers = [];
    protected mixed $content = '';

    public function __construct(mixed $content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status  = $status;
        $this->headers = $headers;
    }

    public function setStatusCode(int $code)
    {
        $this->status = $code;
        http_response_code($code);
    }

    public function header(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function json(mixed $data, int $status = 200): self
    {
        $this->status = $status;
        $this->headers['Content-Type'] = 'application/json';
        $this->content = json_encode($data, JSON_UNESCAPED_UNICODE);
        return $this;
    }

    public function view(string $view, array $params = []): self
    {
        $this->headers['Content-Type'] = 'text/html';
        ob_start();
        extract($params);
        include Application::$ROOT_DIR . "/app/views/{$view}.php";
        $this->content = ob_get_clean();
        return $this;
    }

    public function redirect(string $url, int $status = 302): self
    {
        $this->status = $status;
        $this->headers['Location'] = $url;
        $this->content = '';
        return $this;
    }

    public function send()
    {
        http_response_code($this->status);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        echo $this->content;
    }

    public function getContent()
    {
        return (string) $this->content;
    }
}

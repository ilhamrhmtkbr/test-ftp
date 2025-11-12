<?php

namespace ilhamrhmtkbr\App\Facades;

class Request
{
    public function get(string|int|null $key = null)
    {
        if ($key === null) {
            return $_GET;
        }

        return (isset($_GET[$key]) && $_GET[$key] != null && $_GET[$key] != '') ? $_GET[$key] : null;
    }

    public function post(string|int|null $key = null)
    {
        if ($key === null) {
            return $_POST;
        }

        return (isset($_POST[$key]) && $_POST[$key] != null && $_POST[$key] != '') ? $_POST[$key] : null;
    }

    public function files($key = null)
    {
        if ($key === null) {
            return $_FILES;
        }

        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }

    public function __get($name)
    {
        return $this->post($name) ?? $this->get($name) ?? $this->files($name);
    }
}

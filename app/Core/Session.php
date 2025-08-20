<?php

namespace App\Core;

class Session
{
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function destroy()
    {
        session_unset();
        session_destroy();
    }
}

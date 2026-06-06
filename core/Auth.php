<?php

class Auth
{
    public static function login(array $user)
    {
        session_regenerate_id(true);

        $_SESSION["user"] = [
            "id" => $user["id"],
            "name" => $user["name"],
            "role" => $user["role"],
            "avatar" => $user["avatar"] ?? null,
            "doctor_photo" => null
        ];
    }

    public static function logout()
    {
        session_unset();
        session_destroy();
    }

    public static function check()
    {
        return isset($_SESSION["user"]);
    }

    public static function currentUser()
    {
        return $_SESSION["user"] ?? null;
    }

    public static function role()
    {
        return $_SESSION["user"]["role"] ?? "";
    }

    public static function requireRole(string ...$roles)
    {
        if (!self::check()) {
            header("Location: index.php?page=login");
            exit;
        }

        if (!in_array(self::role(), $roles)) {
            header("Location: index.php?page=error403");
            exit;
        }
    }
}
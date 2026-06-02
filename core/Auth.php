<?php

class Auth
{
    // Store the logged-in user data inside the session
    public static function login(array $user): void
    {
        // Regenerate session ID after login to prevent session fixation attacks
        session_regenerate_id(true);

        // Keep only the needed user data in the session
        $_SESSION["user"] = [
            "id" => $user["id"],
            "name" => $user["name"],
            "role" => $user["role"]
        ];
    }

    // Log the user out and destroy the session
    public static function logout(): void
    {
        // Remove all session variables
        session_unset();

        // Destroy the current session completely
        session_destroy();
    }

    // Check if a user is currently logged in
    public static function check(): bool
    {
        return isset($_SESSION["user"]);
    }

    // Return the current logged-in user data, or null if not logged in
    public static function currentUser(): ?array
    {
        return $_SESSION["user"] ?? null;
    }

    // Return the current user role, or an empty string if no user is logged in
    public static function role(): string
    {
        return $_SESSION["user"]["role"] ?? "";
    }

    // Require the user to have one of the allowed roles
    public static function requireRole(string ...$roles): void
    {
        // If the user is not logged in, redirect to the login page
        if (!self::check()) {
            redirect(BASE_URL . "index.php?page=auth&action=login");
        }

        // If the user role is not allowed, show the 403 page
        if (!in_array(self::role(), $roles)) {
            require_once __DIR__ . "/../views/errors/403.php";
            exit;
        }
    }
}
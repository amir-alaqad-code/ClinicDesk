<?php

class CSRF
{
    // Generate a CSRF token and store it in the session
    public static function generateToken(): string
    {
        // If a token already exists, reuse it instead of replacing it
        if (isset($_SESSION["csrf_token"])) {
            return $_SESSION["csrf_token"];
        }

        // Create a secure random token
        $token = bin2hex(random_bytes(32));

        // Store the token in the session
        $_SESSION["csrf_token"] = $token;

        return $token;
    }

    // Validate the submitted CSRF token
    public static function validateToken(string $token): bool
    {
        // If there is no token in the session, the request is invalid
        if (!isset($_SESSION["csrf_token"])) {
            return false;
        }

        // Compare tokens safely to prevent timing attacks
        return hash_equals($_SESSION["csrf_token"], $token);
    }
}
<?php

// Redirect the user to another page
function redirect(string $url): void
{
    header("Location: " . $url);
    exit;
}

// Clean text before showing it in HTML to prevent XSS attacks
function sanitize(?string $value): string
{
    return htmlspecialchars(trim($value ?? ""), ENT_QUOTES, "UTF-8");
}

// Format a date value in a consistent way
function formatDate(?string $date): string
{
    if (!$date) {
        return "";
    }

    return date("Y-m-d", strtotime($date));
}

// Format a time value in a consistent way
function formatTime(?string $time): string
{
    if (!$time) {
        return "";
    }

    return date("H:i", strtotime($time));
}

// Store a flash message in the session
function setFlash(string $type, string $message): void
{
    $_SESSION["flash"] = [
        "type" => $type,
        "message" => $message
    ];
}

// Return the flash message and remove it from the session
function getFlash(): ?array
{
    if (!isset($_SESSION["flash"])) {
        return null;
    }

    $flash = $_SESSION["flash"];
    unset($_SESSION["flash"]);

    return $flash;
}
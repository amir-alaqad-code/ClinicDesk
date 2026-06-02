<?php

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../models/UserModel.php";

class AuthController
{
    // Show login form or handle login submit
    public function login(): void
    {
        if (Auth::check()) {
            redirect(BASE_URL . "index.php?page=dashboard");
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handleLogin();
            return;
        }

        require_once __DIR__ . "/../views/auth/login.php";
    }

    // Validate login data and create a session
    private function handleLogin(): void
    {
        $csrfToken = $_POST["csrf_token"] ?? "";

        if (!CSRF::validateToken($csrfToken)) {
            setFlash("error", "Invalid request. Please try again.");
            redirect(BASE_URL . "index.php?page=auth&action=login");
        }

        $email = filter_var($_POST["email"] ?? "", FILTER_SANITIZE_EMAIL);
        $password = $_POST["password"] ?? "";

        if ($email === "" || $password === "") {
            setFlash("error", "Email and password are required.");
            redirect(BASE_URL . "index.php?page=auth&action=login");
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            setFlash("error", "Invalid credentials.");
            redirect(BASE_URL . "index.php?page=auth&action=login");
        }

        if ((int) $user["is_active"] !== 1) {
            setFlash("error", "Account suspended. Contact admin.");
            redirect(BASE_URL . "index.php?page=auth&action=login");
        }

        if (!password_verify($password, $user["password"])) {
            setFlash("error", "Invalid credentials.");
            redirect(BASE_URL . "index.php?page=auth&action=login");
        }

        Auth::login($user);

        redirect(BASE_URL . "index.php?page=dashboard");
    }

    // Logout must be POST with CSRF protection
    public function logout(): void
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            require_once __DIR__ . "/../views/errors/403.php";
            exit;
        }

        $csrfToken = $_POST["csrf_token"] ?? "";

        if (!CSRF::validateToken($csrfToken)) {
            require_once __DIR__ . "/../views/errors/403.php";
            exit;
        }

        Auth::logout();

        redirect(BASE_URL . "index.php?page=auth&action=login");
    }
}
<?php

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";
require_once __DIR__ . "/../models/SpecializationModel.php";

class UserController
{
    // Show users list for admin only
    public function index(): void
    {
        Auth::requireRole("admin");

        $pageTitle = "Users";

        // Get current page number from URL, default is page 1
        $currentPage = isset($_GET["p"]) ? max(1, (int) $_GET["p"]) : 1;

        // Optional role filter from URL
        $roleFilter = $_GET["role"] ?? "";

        // Allow only valid role filter values
        if (!in_array($roleFilter, ["", "admin", "doctor", "patient"])) {
            $roleFilter = "";
        }

        $userModel = new UserModel();

        // Get users count for later pagination
        $totalUsers = $userModel->countAll($roleFilter);

        // Get users for the current page
        $users = $userModel->getAllPaginated($currentPage, $roleFilter);

        require_once __DIR__ . "/../views/users/index.php";
    }

    // Show create user form or handle form submit
    public function create(): void
    {
        Auth::requireRole("admin");

        $pageTitle = "Create User";

        $specializationModel = new SpecializationModel();
        $specializations = $specializationModel->getAll();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->store();
            return;
        }

        require_once __DIR__ . "/../views/users/create.php";
    }

    // Store a new user in the database
    private function store(): void
    {
        $csrfToken = $_POST["csrf_token"] ?? "";

        if (!CSRF::validateToken($csrfToken)) {
            setFlash("error", "Invalid request. Please try again.");
            redirect(BASE_URL . "index.php?page=users&action=create");
        }

        $name = trim($_POST["name"] ?? "");
        $email = filter_var($_POST["email"] ?? "", FILTER_SANITIZE_EMAIL);
        $password = $_POST["password"] ?? "";
        $role = $_POST["role"] ?? "patient";
        $phone = trim($_POST["phone"] ?? "");

        if ($name === "" || $email === "" || $password === "") {
            setFlash("error", "Name, email, and password are required.");
            redirect(BASE_URL . "index.php?page=users&action=create");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash("error", "Please enter a valid email address.");
            redirect(BASE_URL . "index.php?page=users&action=create");
        }

        if (!in_array($role, ["admin", "doctor", "patient"])) {
            setFlash("error", "Invalid user role.");
            redirect(BASE_URL . "index.php?page=users&action=create");
        }

        if (strlen($password) < 8) {
            setFlash("error", "Password must be at least 8 characters.");
            redirect(BASE_URL . "index.php?page=users&action=create");
        }

        $userModel = new UserModel();

        if ($userModel->findByEmail($email)) {
            setFlash("error", "Email already exists.");
            redirect(BASE_URL . "index.php?page=users&action=create");
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $userId = $userModel->create([
            "name" => $name,
            "email" => $email,
            "password" => $passwordHash,
            "role" => $role,
            "phone" => $phone
        ]);

        if ($userId === 0) {
            setFlash("error", "Failed to create user.");
            redirect(BASE_URL . "index.php?page=users&action=create");
        }

        if ($role === "doctor") {
            $specializationId = (int) ($_POST["specialization_id"] ?? 0);
            $bio = trim($_POST["bio"] ?? "");
            $consultationFee = (float) ($_POST["consultation_fee"] ?? 0);
            $availableDays = $_POST["available_days"] ?? [];

            if ($specializationId <= 0 || empty($availableDays)) {
                setFlash("error", "Doctor specialization and available days are required.");
                redirect(BASE_URL . "index.php?page=users&action=create");
            }

            $doctorModel = new DoctorModel();

            $doctorId = $doctorModel->create([
                "user_id" => $userId,
                "specialization_id" => $specializationId,
                "bio" => $bio,
                "consultation_fee" => $consultationFee,
                "available_days" => implode(",", $availableDays)
            ]);

            if ($doctorId === 0) {
                setFlash("error", "User was created, but doctor profile creation failed.");
                redirect(BASE_URL . "index.php?page=users");
            }
        }

        setFlash("success", "User created successfully.");
        redirect(BASE_URL . "index.php?page=users");
    }
    // Activate or deactivate a user account
    public function toggleActive(): void
    {
        Auth::requireRole("admin");

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            require_once __DIR__ . "/../views/errors/403.php";
            exit;
        }

        $csrfToken = $_POST["csrf_token"] ?? "";

        if (!CSRF::validateToken($csrfToken)) {
            setFlash("error", "Invalid request. Please try again.");
            redirect(BASE_URL . "index.php?page=users");
        }

        $targetId = (int) ($_POST["id"] ?? 0);

        if ($targetId <= 0) {
            setFlash("error", "Invalid user ID.");
            redirect(BASE_URL . "index.php?page=users");
        }

        $currentUser = Auth::currentUser();

        if ((int) $currentUser["id"] === $targetId) {
            setFlash("error", "You cannot deactivate your own account.");
            redirect(BASE_URL . "index.php?page=users");
        }

        $userModel = new UserModel();

        $targetUser = $userModel->findById($targetId);

        if (!$targetUser) {
            setFlash("error", "User not found.");
            redirect(BASE_URL . "index.php?page=users");
        }

        $success = $userModel->toggleActive($targetId);

        if (!$success) {
            setFlash("error", "Failed to update user status.");
            redirect(BASE_URL . "index.php?page=users");
        }

        setFlash("success", "User status updated successfully.");
        redirect(BASE_URL . "index.php?page=users");
    }
}

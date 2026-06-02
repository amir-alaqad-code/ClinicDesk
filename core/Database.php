<?php

require_once __DIR__ . "/../config/database.php";

class Database
{
    // Holds the single database instance
    private static ?Database $instance = null;

    // Holds the mysqli connection object
    private mysqli $conn;

    // Private constructor prevents creating objects directly with new Database()
    private function __construct()
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Check if the connection failed
        if ($this->conn->connect_error) {
            throw new RuntimeException("Database connection failed.");
        }

        // Use utf8mb4 to support Arabic and English text correctly
        $this->conn->set_charset("utf8mb4");
    }

    // Return the same database instance every time
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    // Execute a prepared SQL query safely
    public function query(string $sql, string $types = "", array $params = [])
    {
        $stmt = $this->conn->prepare($sql);

        // If the statement cannot be prepared, return false
        if (!$stmt) {
            return false;
        }

        // Bind parameters if the query has values
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // Execute the prepared statement
        $executed = $stmt->execute();

        // Return false if execution failed
        if (!$executed) {
            return false;
        }

        // Return result object for SELECT queries
        $result = $stmt->get_result();

        if ($result !== false) {
            return $result;
        }

        // Return true for INSERT, UPDATE, and DELETE queries
        return true;
    }

    // Return the last inserted record ID
    public function lastInsertId(): int
    {
        return $this->conn->insert_id;
    }
}
<?php

require_once __DIR__ . "/../core/Database.php";

abstract class BaseModel
{
    // Shared database instance for all models
    protected Database $db;

    // Load the single Database instance when any model is created
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Execute SQL queries through one shared method
    protected function execute(string $sql, string $types = "", array $params = [])
    {
        return $this->db->query($sql, $types, $params);
    }
}
<?php

require_once __DIR__ . '/../config/Database.php';

class User {

    private $conn;

    public function __construct() {

        $database = new Database();
        $this->conn = $database->connect();
    }

    public function create($name, $email, $password, $role) {

        $query = "INSERT INTO users
                  (name, email, password_hash, role)
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            $name,
            $email,
            $password,
            $role
        ]);
    }

    public function findByEmail($email) {

        $query = "SELECT * FROM users WHERE email = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<?php

require_once __DIR__ . '/../config/Database.php';

class Quiz {

    private $conn;

    public function __construct() {

        $database = new Database();

        $this->conn = $database->connect();
    }

    public function create(
        $instructor_id,
        $title,
        $description,
        $time_limit_minutes,
        $status
    ) {

        $query = "INSERT INTO quizzes
        (instructor_id, title, description,
        time_limit_minutes, status)
        VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            $instructor_id,
            $title,
            $description,
            $time_limit_minutes,
            $status
        ]);
    }

    public function getInstructorQuizzes($instructor_id) {

        $query = "SELECT * FROM quizzes
                  WHERE instructor_id = ?
                  ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([$instructor_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {

        $query = "SELECT * FROM quizzes WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPublishedQuizzes() {

        $query = "SELECT * FROM quizzes
                  WHERE status = 'published'
                  ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleStatus($id) {

        $query = "UPDATE quizzes
                  SET status =
                  IF(status='draft',
                     'published',
                     'draft')
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([$id]);
    }
}
?>

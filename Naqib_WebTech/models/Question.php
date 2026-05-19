<?php

require_once __DIR__ . '/../config/Database.php';

class Question {

    private $conn;

    public function __construct() {

        $database = new Database();

        $this->conn = $database->connect();
    }

    public function create(
        $quiz_id,
        $question_text,
        $marks,
        $order_index
    ) {

        $query = "INSERT INTO questions
        (quiz_id, question_text, marks, order_index)
        VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            $quiz_id,
            $question_text,
            $marks,
            $order_index
        ]);

        return $this->conn->lastInsertId();
    }

    public function getByQuiz($quiz_id) {

        $query = "SELECT * FROM questions
                  WHERE quiz_id = ?
                  ORDER BY order_index ASC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([$quiz_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(
        $id,
        $question_text,
        $marks
    ) {

        $query = "UPDATE questions
                  SET question_text = ?,
                      marks = ?
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            $question_text,
            $marks,
            $id
        ]);
    }

    public function delete($id) {

        $query = "DELETE FROM questions
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([$id]);
    }
}
?>

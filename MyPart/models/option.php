<?php

require_once __DIR__ . '/../config/Database.php';

class Option {

    private $conn;

    public function __construct() {

        $database = new Database();

        $this->conn = $database->connect();
    }

    public function create(
        $question_id,
        $option_text,
        $is_correct
    ) {

        $query = "INSERT INTO options
        (question_id, option_text, is_correct)
        VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            $question_id,
            $option_text,
            $is_correct
        ]);
    }

    public function getByQuestion($question_id) {

        $query = "SELECT * FROM options
                  WHERE question_id = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([$question_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {

        $query = "SELECT * FROM options WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByIdAndQuestion($id, $question_id) {

        $query = "SELECT * FROM options
                  WHERE id = ?
                  AND question_id = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            $id,
            $question_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(
        $id,
        $option_text,
        $is_correct
    ) {

        $query = "UPDATE options
                  SET option_text = ?,
                      is_correct = ?
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            $option_text,
            $is_correct,
            $id
        ]);
    }

    public function deleteByQuestion($question_id) {

        $query = "DELETE FROM options
                  WHERE question_id = ?";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([$question_id]);
    }
}
?>

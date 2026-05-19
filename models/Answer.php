<?php

require_once __DIR__ . '/../config/Database.php';

class Answer {

    private $conn;

    public function __construct() {

        $database = new Database();

        $this->conn = $database->connect();
    }

    public function create(
        $attempt_id,
        $question_id,
        $selected_option_id
    ) {

        $query = "INSERT INTO answers
                  (attempt_id, question_id, selected_option_id)
                  VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            $attempt_id,
            $question_id,
            $selected_option_id
        ]);
    }

    public function deleteByAttempt($attempt_id) {

        $query = "DELETE FROM answers
                  WHERE attempt_id = ?";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([$attempt_id]);
    }

    public function getByAttempt($attempt_id) {

        $query = "SELECT * FROM answers
                  WHERE attempt_id = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([$attempt_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResultDetails($attempt_id) {

        $query = "SELECT questions.question_text,
                         questions.marks,
                         selected_options.option_text AS selected_answer,
                         selected_options.is_correct,
                         correct_options.option_text AS correct_answer
                  FROM answers
                  INNER JOIN questions
                  ON questions.id = answers.question_id
                  LEFT JOIN options AS selected_options
                  ON selected_options.id = answers.selected_option_id
                  LEFT JOIN options AS correct_options
                  ON correct_options.question_id = questions.id
                  AND correct_options.is_correct = 1
                  WHERE answers.attempt_id = ?
                  ORDER BY questions.order_index ASC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([$attempt_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

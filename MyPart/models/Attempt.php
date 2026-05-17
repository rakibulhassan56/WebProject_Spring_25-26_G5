<?php

require_once __DIR__ . '/../config/Database.php';

class Attempt {

    private $conn;

    public function __construct() {

        $database = new Database();

        $this->conn = $database->connect();
    }

    public function create($quiz_id, $student_id) {

        $query = "INSERT INTO attempts
                  (quiz_id, student_id, started_at)
                  VALUES (?, ?, NOW())";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            $quiz_id,
            $student_id
        ]);

        return $this->conn->lastInsertId();
    }

    public function getById($id) {

        $query = "SELECT * FROM attempts WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByQuizAndStudent($quiz_id, $student_id) {

        $query = "SELECT * FROM attempts
                  WHERE quiz_id = ?
                  AND student_id = ?
                  ORDER BY id DESC
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            $quiz_id,
            $student_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function complete($id, $score) {

        $query = "UPDATE attempts
                  SET score = ?,
                      completed_at = NOW()
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            $score,
            $id
        ]);
    }

    public function getCompletedByStudent($student_id) {

        $query = "SELECT attempts.*,
                         quizzes.title,
                         quizzes.time_limit_minutes,
                         (
                            SELECT SUM(questions.marks)
                            FROM questions
                            WHERE questions.quiz_id = quizzes.id
                         ) AS total_marks
                  FROM attempts
                  INNER JOIN quizzes
                  ON quizzes.id = attempts.quiz_id
                  WHERE attempts.student_id = ?
                  AND attempts.completed_at IS NOT NULL
                  ORDER BY attempts.completed_at DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([$student_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAnalyticsByInstructor($instructor_id) {

        $query = "SELECT quizzes.id,
                         quizzes.title,
                         COUNT(attempts.id) AS attempt_count,
                         AVG(attempts.score) AS average_score,
                         MAX(attempts.score) AS highest_score,
                         MIN(attempts.score) AS lowest_score,
                         (
                            SELECT SUM(questions.marks)
                            FROM questions
                            WHERE questions.quiz_id = quizzes.id
                         ) AS total_marks
                  FROM quizzes
                  LEFT JOIN attempts
                  ON attempts.quiz_id = quizzes.id
                  AND attempts.completed_at IS NOT NULL
                  WHERE quizzes.instructor_id = ?
                  GROUP BY quizzes.id
                  ORDER BY quizzes.id DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([$instructor_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLeaderboard($quiz_id = null) {

        $params = [];

        $query = "SELECT users.name,
                         quizzes.title,
                         attempts.score,
                         attempts.completed_at,
                         (
                            SELECT SUM(questions.marks)
                            FROM questions
                            WHERE questions.quiz_id = quizzes.id
                         ) AS total_marks
                  FROM attempts
                  INNER JOIN users
                  ON users.id = attempts.student_id
                  INNER JOIN quizzes
                  ON quizzes.id = attempts.quiz_id
                  WHERE attempts.completed_at IS NOT NULL";

        if($quiz_id) {

            $query .= " AND attempts.quiz_id = ?";

            $params[] = $quiz_id;
        }

        $query .= " ORDER BY attempts.score DESC,
                            attempts.completed_at ASC
                    LIMIT 10";

        $stmt = $this->conn->prepare($query);

        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

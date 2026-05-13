<?php

declare(strict_types=1);

final class Attempt
{
    public function __construct(private PDO $db)
    {
    }

    public function hasCompleted(int $studentId, int $quizId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM attempts WHERE student_id = ? AND quiz_id = ? AND completed_at IS NOT NULL LIMIT 1'
        );
        $stmt->execute([$studentId, $quizId]);
        return (bool) $stmt->fetchColumn();
    }

    public function getCompletedScore(int $studentId, int $quizId): ?int
    {
        $stmt = $this->db->prepare(
            'SELECT score FROM attempts WHERE student_id = ? AND quiz_id = ? AND completed_at IS NOT NULL ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute([$studentId, $quizId]);
        $v = $stmt->fetchColumn();
        return $v === false ? null : (int) $v;
    }

    public function start(int $studentId, int $quizId): int
    {
        if ($this->hasCompleted($studentId, $quizId)) {
            throw new RuntimeException('already_completed');
        }
        $stmt = $this->db->prepare(
            'INSERT INTO attempts (quiz_id, student_id, score, started_at, completed_at) VALUES (?, ?, NULL, NOW(), NULL)'
        );
        $stmt->execute([$quizId, $studentId]);
        return (int) $this->db->lastInsertId();
    }

    public function findForStudent(int $attemptId, int $studentId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, q.title AS quiz_title, q.total_marks, q.time_limit_minutes
             FROM attempts a
             INNER JOIN quizzes q ON q.id = a.quiz_id
             WHERE a.id = ? AND a.student_id = ? LIMIT 1'
        );
        $stmt->execute([$attemptId, $studentId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findWithQuiz(int $attemptId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, q.title AS quiz_title, q.total_marks, u.name AS student_name
             FROM attempts a
             INNER JOIN quizzes q ON q.id = a.quiz_id
             INNER JOIN users u ON u.id = a.student_id
             WHERE a.id = ? LIMIT 1'
        );
        $stmt->execute([$attemptId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * @param array<int,int> $answers question_id => selected_option_id
     */
    public function submitAndGrade(int $attemptId, int $studentId, array $answers): array
    {
        $attempt = $this->findForStudent($attemptId, $studentId);
        if ($attempt === null) {
            throw new RuntimeException('not_found');
        }
        if ($attempt['completed_at'] !== null) {
            throw new RuntimeException('already_submitted');
        }
        $quizId = (int) $attempt['quiz_id'];
        if ($this->hasCompleted($studentId, $quizId)) {
            throw new RuntimeException('already_completed');
        }

        $this->db->beginTransaction();
        try {
            $score = 0;
            $ins = $this->db->prepare(
                'INSERT INTO answers (attempt_id, question_id, selected_option_id) VALUES (?, ?, ?)'
            );
            $chk = $this->db->prepare(
                'SELECT o.is_correct, q.marks
                 FROM options o
                 INNER JOIN questions q ON q.id = o.question_id
                 WHERE o.id = ? AND q.quiz_id = ? AND q.id = ? LIMIT 1'
            );
            foreach ($answers as $questionId => $optionId) {
                $questionId = (int) $questionId;
                $optionId = (int) $optionId;
                $chk->execute([$optionId, $quizId, $questionId]);
                $row = $chk->fetch();
                if ($row === false) {
                    continue;
                }
                if ((int) $row['is_correct'] === 1) {
                    $score += (int) $row['marks'];
                }
                $ins->execute([$attemptId, $questionId, $optionId]);
            }
            $upd = $this->db->prepare(
                'UPDATE attempts SET score = ?, completed_at = NOW() WHERE id = ? AND student_id = ? AND completed_at IS NULL'
            );
            $upd->execute([$score, $attemptId, $studentId]);
            $this->db->commit();
            return ['score' => $score, 'quiz_id' => $quizId];
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function listForStudent(int $studentId): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, q.title AS quiz_title, q.total_marks
             FROM attempts a
             INNER JOIN quizzes q ON q.id = a.quiz_id
             WHERE a.student_id = ? AND a.completed_at IS NOT NULL
             ORDER BY a.completed_at DESC"
        );
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function listForQuizInstructor(int $quizId, int $instructorId): array
    {
        $own = $this->db->prepare('SELECT 1 FROM quizzes WHERE id = ? AND instructor_id = ? LIMIT 1');
        $own->execute([$quizId, $instructorId]);
        if (!$own->fetchColumn()) {
            return [];
        }
        $stmt = $this->db->prepare(
            "SELECT a.*, u.name AS student_name
             FROM attempts a
             INNER JOIN users u ON u.id = a.student_id
             WHERE a.quiz_id = ? AND a.completed_at IS NOT NULL
             ORDER BY a.completed_at DESC"
        );
        $stmt->execute([$quizId]);
        return $stmt->fetchAll();
    }

    public function leaderboardTop(int $limit = 10): array
    {
        $limit = max(1, min(100, $limit));
        $stmt = $this->db->prepare(
            'SELECT u.id, u.name, COALESCE(SUM(a.score), 0) AS cumulative
             FROM users u
             INNER JOIN attempts a ON a.student_id = u.id AND a.completed_at IS NOT NULL
             WHERE u.role = \'student\'
             GROUP BY u.id, u.name
             ORDER BY cumulative DESC
             LIMIT ' . (int) $limit
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function openAttemptId(int $studentId, int $quizId): ?int
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM attempts WHERE student_id = ? AND quiz_id = ? AND completed_at IS NULL ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute([$studentId, $quizId]);
        $v = $stmt->fetchColumn();
        return $v === false ? null : (int) $v;
    }
}

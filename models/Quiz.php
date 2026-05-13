<?php

declare(strict_types=1);

final class Quiz
{
    public function __construct(private PDO $db)
    {
    }

    public function recalculateTotalMarks(int $quizId): void
    {
        $stmt = $this->db->prepare('SELECT COALESCE(SUM(marks), 0) FROM questions WHERE quiz_id = ?');
        $stmt->execute([$quizId]);
        $total = (int) $stmt->fetchColumn();
        $u = $this->db->prepare('UPDATE quizzes SET total_marks = ? WHERE id = ?');
        $u->execute([$total, $quizId]);
    }

    public function create(int $instructorId, string $title, string $description, int $timeLimit, string $status): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO quizzes (instructor_id, title, description, total_marks, time_limit_minutes, status)
             VALUES (?, ?, ?, 0, ?, ?)'
        );
        $stmt->execute([$instructorId, $title, $description, $timeLimit, $status]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $quizId, int $instructorId, string $title, string $description, int $timeLimit, string $status): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE quizzes SET title = ?, description = ?, time_limit_minutes = ?, status = ?
             WHERE id = ? AND instructor_id = ?'
        );
        $stmt->execute([$title, $description, $timeLimit, $status, $quizId, $instructorId]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $quizId, int $instructorId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM quizzes WHERE id = ? AND instructor_id = ?');
        $stmt->execute([$quizId, $instructorId]);
        return $stmt->rowCount() > 0;
    }

    public function findOwned(int $quizId, int $instructorId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM quizzes WHERE id = ? AND instructor_id = ? LIMIT 1');
        $stmt->execute([$quizId, $instructorId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $quizId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM quizzes WHERE id = ? LIMIT 1');
        $stmt->execute([$quizId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function listByInstructor(int $instructorId): array
    {
        $stmt = $this->db->prepare(
            'SELECT q.*, (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) AS question_count
             FROM quizzes q WHERE q.instructor_id = ? ORDER BY q.created_at DESC'
        );
        $stmt->execute([$instructorId]);
        return $stmt->fetchAll();
    }

    public function listPublishedForStudent(): array
    {
        $stmt = $this->db->query(
            "SELECT id, title, description, total_marks, time_limit_minutes, status, created_at
             FROM quizzes WHERE status = 'published' ORDER BY created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function toggleStatus(int $quizId, int $instructorId): ?string
    {
        $quiz = $this->findOwned($quizId, $instructorId);
        if ($quiz === null) {
            return null;
        }
        $next = $quiz['status'] === 'published' ? 'draft' : 'published';
        if ($next === 'published') {
            $c = $this->db->prepare('SELECT COUNT(*) FROM questions WHERE quiz_id = ?');
            $c->execute([$quizId]);
            if ((int) $c->fetchColumn() < 1) {
                return 'blocked';
            }
        }
        $stmt = $this->db->prepare('UPDATE quizzes SET status = ? WHERE id = ? AND instructor_id = ?');
        $stmt->execute([$next, $quizId, $instructorId]);
        return $next;
    }
}

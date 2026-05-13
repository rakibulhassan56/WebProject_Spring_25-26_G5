<?php

declare(strict_types=1);

final class User
{
    public function __construct(private PDO $db)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return (bool) $stmt->fetchColumn();
    }

    public function create(string $name, string $email, string $passwordHash, string $role): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password_hash, role, is_active) VALUES (?, ?, ?, ?, 1)'
        );
        $stmt->execute([$name, $email, $passwordHash, $role]);
        return (int) $this->db->lastInsertId();
    }

    public function listAll(): array
    {
        $stmt = $this->db->query(
            'SELECT id, name, email, role, is_active, created_at FROM users ORDER BY created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function setActive(int $userId, bool $active): void
    {
        $stmt = $this->db->prepare('UPDATE users SET is_active = ? WHERE id = ?');
        $stmt->execute([$active ? 1 : 0, $userId]);
    }

    public function studentDashboardStats(int $studentId): array
    {
        $published = (int) $this->db->query(
            "SELECT COUNT(*) FROM quizzes WHERE status = 'published'"
        )->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT COUNT(*) AS attempts, COALESCE(SUM(score), 0) AS total_score
             FROM attempts WHERE student_id = ? AND completed_at IS NOT NULL'
        );
        $stmt->execute([$studentId]);
        $row = $stmt->fetch() ?: ['attempts' => 0, 'total_score' => 0];

        return [
            'published_quizzes' => $published,
            'attempts_taken' => (int) $row['attempts'],
            'total_score' => (int) $row['total_score'],
        ];
    }

    public function instructorDashboardStats(int $instructorId): array
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM quizzes WHERE instructor_id = ?');
        $stmt->execute([$instructorId]);
        $quizCount = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            'SELECT COUNT(a.id) AS cnt
             FROM attempts a
             INNER JOIN quizzes q ON q.id = a.quiz_id
             WHERE q.instructor_id = ?'
        );
        $stmt->execute([$instructorId]);
        $attempts = (int) $stmt->fetchColumn();

        return [
            'quizzes_created' => $quizCount,
            'total_attempts' => $attempts,
        ];
    }
}

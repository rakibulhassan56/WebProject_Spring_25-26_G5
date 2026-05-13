<?php

declare(strict_types=1);

final class Question
{
    public function __construct(private PDO $db)
    {
    }

    public function countForQuiz(int $quizId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM questions WHERE quiz_id = ?');
        $stmt->execute([$quizId]);
        return (int) $stmt->fetchColumn();
    }

    public function nextOrderIndex(int $quizId): int
    {
        $stmt = $this->db->prepare('SELECT COALESCE(MAX(order_index), -1) + 1 FROM questions WHERE quiz_id = ?');
        $stmt->execute([$quizId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * @param list<string> $optionTexts exactly 4
     * @param int $correctIndex 0-3
     */
    public function addMcq(int $quizId, int $instructorId, string $text, int $marks, array $optionTexts, int $correctIndex): bool
    {
        if (!$this->ownsQuiz($quizId, $instructorId)) {
            return false;
        }
        $this->db->beginTransaction();
        try {
            $order = $this->nextOrderIndex($quizId);
            $stmt = $this->db->prepare(
                'INSERT INTO questions (quiz_id, question_text, marks, order_index) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([$quizId, $text, $marks, $order]);
            $qid = (int) $this->db->lastInsertId();
            $ins = $this->db->prepare(
                'INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)'
            );
            foreach ($optionTexts as $i => $label) {
                $ins->execute([$qid, $label, $i === $correctIndex ? 1 : 0]);
            }
            $this->db->commit();
            (new Quiz($this->db))->recalculateTotalMarks($quizId);
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function listForQuizWithOptions(int $quizId, int $instructorId): array
    {
        if (!$this->ownsQuiz($quizId, $instructorId)) {
            return [];
        }
        $stmt = $this->db->prepare(
            'SELECT * FROM questions WHERE quiz_id = ? ORDER BY order_index ASC, id ASC'
        );
        $stmt->execute([$quizId]);
        $questions = $stmt->fetchAll();
        foreach ($questions as &$q) {
            $o = $this->db->prepare('SELECT * FROM options WHERE question_id = ? ORDER BY id ASC');
            $o->execute([(int) $q['id']]);
            $q['options'] = $o->fetchAll();
        }
        unset($q);
        return $questions;
    }

    public function listForQuizTaking(int $quizId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM questions WHERE quiz_id = ? ORDER BY order_index ASC, id ASC'
        );
        $stmt->execute([$quizId]);
        $questions = $stmt->fetchAll();
        foreach ($questions as &$q) {
            $o = $this->db->prepare('SELECT id, option_text FROM options WHERE question_id = ? ORDER BY id ASC');
            $o->execute([(int) $q['id']]);
            $q['options'] = $o->fetchAll();
        }
        unset($q);
        return $questions;
    }

    public function updateMcq(int $questionId, int $instructorId, string $text, array $optionTextsById, int $correctOptionId): bool
    {
        $q = $this->findWithQuiz($questionId);
        if ($q === null || !$this->ownsQuiz((int) $q['quiz_id'], $instructorId)) {
            return false;
        }
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('UPDATE questions SET question_text = ? WHERE id = ?');
            $stmt->execute([$text, $questionId]);
            $opts = $this->db->prepare('SELECT id FROM options WHERE question_id = ? ORDER BY id ASC');
            $opts->execute([$questionId]);
            $ids = $opts->fetchAll(PDO::FETCH_COLUMN);
            foreach ($ids as $oid) {
                $oid = (int) $oid;
                if (!isset($optionTextsById[$oid])) {
                    continue;
                }
                $isCorrect = $oid === $correctOptionId ? 1 : 0;
                $u = $this->db->prepare('UPDATE options SET option_text = ?, is_correct = ? WHERE id = ? AND question_id = ?');
                $u->execute([$optionTextsById[$oid], $isCorrect, $oid, $questionId]);
            }
            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete(int $questionId, int $instructorId): ?int
    {
        $q = $this->findWithQuiz($questionId);
        if ($q === null) {
            return null;
        }
        $quizId = (int) $q['quiz_id'];
        if (!$this->ownsQuiz($quizId, $instructorId)) {
            return null;
        }
        $stmt = $this->db->prepare('DELETE FROM questions WHERE id = ?');
        $stmt->execute([$questionId]);
        if ($stmt->rowCount() > 0) {
            (new Quiz($this->db))->recalculateTotalMarks($quizId);
            return $quizId;
        }
        return null;
    }

    private function ownsQuiz(int $quizId, int $instructorId): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM quizzes WHERE id = ? AND instructor_id = ? LIMIT 1');
        $stmt->execute([$quizId, $instructorId]);
        return (bool) $stmt->fetchColumn();
    }

    private function findWithQuiz(int $questionId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT q.id AS question_id, q.quiz_id FROM questions q WHERE q.id = ? LIMIT 1'
        );
        $stmt->execute([$questionId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getBreakdownForAttempt(int $attemptId): array
    {
        $stmt = $this->db->prepare(
            'SELECT qu.question_text, qu.marks,
                    o_sel.option_text AS selected_text,
                    o_sel.id AS selected_id,
                    o_ok.option_text AS correct_text,
                    CASE WHEN o_sel.is_correct = 1 THEN 1 ELSE 0 END AS is_correct
             FROM answers a
             INNER JOIN questions qu ON qu.id = a.question_id
             INNER JOIN options o_sel ON o_sel.id = a.selected_option_id
             INNER JOIN options o_ok ON o_ok.question_id = qu.id AND o_ok.is_correct = 1
             WHERE a.attempt_id = ?
             ORDER BY qu.order_index ASC, qu.id ASC'
        );
        $stmt->execute([$attemptId]);
        return $stmt->fetchAll();
    }
}

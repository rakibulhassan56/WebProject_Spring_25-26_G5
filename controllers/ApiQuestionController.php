<?php

declare(strict_types=1);

final class ApiQuestionController
{
    public function __construct(private PDO $db)
    {
    }

    public function patch(): void
    {
        require_api_role(['instructor']);
        $uid = (int) $_SESSION['user_id'];
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            json_response(['ok' => false, 'error' => 'invalid_id'], 422);
        }
        $body = read_json_body();
        $text = trim((string) ($body['question_text'] ?? ''));
        $correctOptionId = (int) ($body['correct_option_id'] ?? 0);
        $options = $body['options'] ?? null;
        if ($text === '' || !is_array($options)) {
            json_response(['ok' => false, 'error' => 'validation'], 422);
        }
        $map = [];
        foreach ($options as $oid => $label) {
            $map[(int) $oid] = trim((string) $label);
        }
        if ($correctOptionId <= 0) {
            json_response(['ok' => false, 'error' => 'validation'], 422);
        }
        $chk = $this->db->prepare('SELECT COUNT(*) FROM options WHERE id = ? AND question_id = ?');
        $chk->execute([$correctOptionId, $id]);
        if ((int) $chk->fetchColumn() !== 1) {
            json_response(['ok' => false, 'error' => 'invalid_correct_option'], 422);
        }
        $ok = (new Question($this->db))->updateMcq($id, $uid, $text, $map, $correctOptionId);
        if (!$ok) {
            json_response(['ok' => false, 'error' => 'update_failed'], 400);
        }
        $stmt = $this->db->prepare('SELECT quiz_id FROM questions WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $quizId = (int) $stmt->fetchColumn();
        $fresh = (new Question($this->db))->listForQuizWithOptions($quizId, $uid);
        $row = null;
        foreach ($fresh as $q) {
            if ((int) $q['id'] === $id) {
                $row = $q;
                break;
            }
        }
        json_response(['ok' => true, 'question' => $row]);
    }

    public function delete(): void
    {
        require_api_role(['instructor']);
        $uid = (int) $_SESSION['user_id'];
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            json_response(['ok' => false, 'error' => 'invalid_id'], 422);
        }
        $res = (new Question($this->db))->delete($id, $uid);
        if ($res === null) {
            json_response(['ok' => false, 'error' => 'not_found'], 404);
        }
        json_response(['ok' => true, 'id' => $id]);
    }
}

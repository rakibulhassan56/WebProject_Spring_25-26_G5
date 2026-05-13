<?php

declare(strict_types=1);

final class ApiQuizController
{
    public function __construct(private PDO $db)
    {
    }

    public function toggle(): void
    {
        require_api_role(['instructor']);
        $uid = (int) $_SESSION['user_id'];
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            json_response(['ok' => false, 'error' => 'invalid_id'], 422);
        }
        $next = (new Quiz($this->db))->toggleStatus($id, $uid);
        if ($next === null) {
            json_response(['ok' => false, 'error' => 'not_found'], 404);
        }
        if ($next === 'blocked') {
            json_response(['ok' => false, 'error' => 'needs_questions'], 422);
        }
        json_response(['ok' => true, 'status' => $next, 'label' => $next === 'published' ? 'Unpublish' : 'Publish']);
    }

    public function submit(): void
    {
        require_api_role(['student']);
        $uid = (int) $_SESSION['user_id'];
        $body = read_json_body();
        $attemptId = (int) ($body['attempt_id'] ?? 0);
        $answers = $body['answers'] ?? [];
        if ($attemptId <= 0 || !is_array($answers)) {
            json_response(['ok' => false, 'error' => 'validation'], 422);
        }
        $normalized = [];
        foreach ($answers as $qid => $oid) {
            $normalized[(int) $qid] = (int) $oid;
        }
        try {
            $result = (new Attempt($this->db))->submitAndGrade($attemptId, $uid, $normalized);
            json_response([
                'ok' => true,
                'score' => $result['score'],
                'redirect' => url('student/quiz/result', ['attempt_id' => $attemptId]),
            ]);
        } catch (RuntimeException $e) {
            $code = match ($e->getMessage()) {
                'not_found' => 404,
                'already_submitted', 'already_completed' => 409,
                default => 400,
            };
            json_response(['ok' => false, 'error' => $e->getMessage()], $code);
        }
    }
}

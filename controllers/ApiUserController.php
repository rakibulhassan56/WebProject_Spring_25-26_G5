<?php

declare(strict_types=1);

final class ApiUserController
{
    public function __construct(private PDO $db)
    {
    }

    public function toggle(): void
    {
        if (($_SESSION['role'] ?? '') !== 'admin') {
            json_response(['ok' => false, 'error' => 'forbidden'], 403);
        }
        if (empty($_SESSION['user_id'])) {
            json_response(['ok' => false, 'error' => 'unauthorized'], 401);
        }
        $body = read_json_body();
        $userId = (int) ($body['user_id'] ?? 0);
        if ($userId <= 0) {
            json_response(['ok' => false, 'error' => 'invalid_user'], 422);
        }
        $adminId = (int) $_SESSION['user_id'];
        if ($userId === $adminId) {
            json_response(['ok' => false, 'error' => 'cannot_toggle_self'], 422);
        }
        $user = (new User($this->db))->findById($userId);
        if ($user === null) {
            json_response(['ok' => false, 'error' => 'not_found'], 404);
        }
        $next = (int) $user['is_active'] === 1 ? 0 : 1;
        (new User($this->db))->setActive($userId, $next === 1);
        json_response([
            'ok' => true,
            'user_id' => $userId,
            'is_active' => $next,
            'label' => $next === 1 ? 'Suspend' : 'Activate',
        ]);
    }
}

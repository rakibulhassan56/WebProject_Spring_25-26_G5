<?php

declare(strict_types=1);

final class ApiLeaderboardController
{
    public function __construct(private PDO $db)
    {
    }

    public function index(): void
    {
        $rows = (new Attempt($this->db))->leaderboardTop(10);
        json_response(['ok' => true, 'rows' => $rows]);
    }
}

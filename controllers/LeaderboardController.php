<?php

declare(strict_types=1);

final class LeaderboardController
{
    public function __construct(private PDO $db)
    {
    }

    public function page(): void
    {
        view('leaderboard/page', [
            'title' => 'Leaderboard',
        ]);
    }
}

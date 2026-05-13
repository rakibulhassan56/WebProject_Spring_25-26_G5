<?php

declare(strict_types=1);

final class AdminController
{
    public function __construct(private PDO $db)
    {
    }

    public function users(): void
    {
        require_role(['admin']);
        $users = (new User($this->db))->listAll();
        view('admin/users', ['title' => 'User management', 'users' => $users]);
    }
}

<?php

require_once __DIR__ . '/../models/Attempt.php';

class ApiLeaderboardController {

    public function index() {

        header('Content-Type: application/json');

        $quiz_id = $_GET['quiz_id'] ?? null;

        $attemptModel = new Attempt();

        echo json_encode(
            $attemptModel->getLeaderboard($quiz_id)
        );
    }
}
?>

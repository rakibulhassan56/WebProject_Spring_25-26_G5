<?php

require_once 'models/Attempt.php';
require_once 'models/Quiz.php';

class LeaderboardController {

    public function index() {

        require 'middleware/auth.php';

        $attemptModel = new Attempt();
        $quizModel = new Quiz();

        $quiz_id = $_GET['quiz_id'] ?? null;
        $leaderboard = $attemptModel->getLeaderboard($quiz_id);
        $quizzes = $quizModel->getPublishedQuizzes();

        require 'Views/leaderboard/index.php';
    }
}
?>

<?php

require_once 'models/Attempt.php';

class AnalyticsController {

    public function instructor() {

        require 'middleware/instructor.php';

        $attemptModel = new Attempt();

        $analytics = $attemptModel->getAnalyticsByInstructor(
            $_SESSION['user_id']
        );

        require 'Views/analytics/instructor.php';
    }
}
?>

<?php

$url = $_GET['url'] ?? 'home';

switch($url) {

    case 'home':
        echo "MVC Project Running";
        break;

    case 'login':

        require 'Controllers/Authcontroller.php';

        $controller = new AuthController();

        $controller->login();

        break;

    case 'register':

        require 'Controllers/Authcontroller.php';

        $controller = new AuthController();

        $controller->register();

        break;

    case 'logout':

        require 'Controllers/Authcontroller.php';

        $controller = new AuthController();

        $controller->logout();

        break;

    case 'student-dashboard':

        require 'Controllers/DashboardController.php';

        $controller = new DashboardController();

        $controller->student();

        break;

    case 'student-quizzes':

        require 'Controllers/StudentQuizController.php';

        $controller = new StudentQuizController();

        $controller->index();

        break;

    case 'start-quiz':

        require 'Controllers/StudentQuizController.php';

        $controller = new StudentQuizController();

        $controller->start();

        break;

    case 'take-quiz':

        require 'Controllers/StudentQuizController.php';

        $controller = new StudentQuizController();

        $controller->take();

        break;

    case 'submit-quiz':

        require 'Controllers/StudentQuizController.php';

        $controller = new StudentQuizController();

        $controller->submit();

        break;

    case 'quiz-result':

        require 'Controllers/StudentQuizController.php';

        $controller = new StudentQuizController();

        $controller->result();

        break;

    case 'my-results':

        require 'Controllers/StudentQuizController.php';

        $controller = new StudentQuizController();

        $controller->myResults();

        break;

    case 'instructor-dashboard':

        require 'Controllers/DashboardController.php';

        $controller = new DashboardController();

        $controller->instructor();

        break;

    case 'instructor-analytics':

        require 'Controllers/AnalyticsController.php';

        $controller = new AnalyticsController();

        $controller->instructor();

        break;

    case 'leaderboard':

        require 'Controllers/LeaderboardController.php';

        $controller = new LeaderboardController();

        $controller->index();

        break;

    case 'admin-dashboard':

        require 'Controllers/DashboardController.php';

        $controller = new DashboardController();

        $controller->admin();

        break;
    case 'create-quiz':

        require 'Controllers/Quizcontroller.php';

        $controller = new QuizController();

        $controller->create();

        break;

    case 'quiz-list':

        require 'Controllers/Quizcontroller.php';

        $controller = new QuizController();

        $controller->index();

        break;

    case 'add-question':

        require 'Controllers/QuestionController.php';

        $controller = new QuestionController();

        $controller->create();

        break;

    default:
        echo "404 Not Found";
        break;
}
?>

<?php

declare(strict_types=1);

final class Router
{
    public function dispatch(string $route, string $method): void
    {
        $pdo = db();
        $route = trim($route, '/');

        if ($route === '' || $route === 'home') {
            $this->home();
            return;
        }

        if (str_starts_with($route, 'api/')) {
            $this->api($pdo, $route, $method);
            return;
        }

        match (true) {
            $route === 'auth/register' && $method === 'GET' => (new AuthController($pdo))->registerForm(),
            $route === 'auth/register' && $method === 'POST' => (new AuthController($pdo))->registerSubmit(),
            $route === 'auth/login' && $method === 'GET' => (new AuthController($pdo))->loginForm(),
            $route === 'auth/login' && $method === 'POST' => (new AuthController($pdo))->loginSubmit(),
            $route === 'auth/logout' && $method === 'GET' => (new AuthController($pdo))->logout(),

            $route === 'student/home' && $method === 'GET' => (new StudentController($pdo))->home(),
            $route === 'student/quizzes' && $method === 'GET' => (new StudentController($pdo))->quizzes(),
            $route === 'student/quiz/start' && $method === 'POST' => (new StudentController($pdo))->startQuiz(),
            $route === 'student/quiz/take' && $method === 'GET' => (new StudentController($pdo))->takeQuiz(),
            $route === 'student/quiz/result' && $method === 'GET' => (new StudentController($pdo))->result(),
            $route === 'student/results' && $method === 'GET' => (new StudentController($pdo))->myResults(),

            $route === 'instructor/home' && $method === 'GET' => (new InstructorController($pdo))->home(),
            $route === 'instructor/quizzes' && $method === 'GET' => (new InstructorController($pdo))->quizzes(),
            $route === 'instructor/quizzes/new' && $method === 'GET' => (new InstructorController($pdo))->quizForm(),
            $route === 'instructor/quizzes/edit' && $method === 'GET' => (new InstructorController($pdo))->quizForm(),
            $route === 'instructor/quizzes/save' && $method === 'POST' => (new InstructorController($pdo))->quizSave(),
            $route === 'instructor/quizzes/delete' && $method === 'POST' => (new InstructorController($pdo))->quizDelete(),
            $route === 'instructor/questions' && $method === 'GET' => (new InstructorController($pdo))->questions(),
            $route === 'instructor/questions/add' && $method === 'POST' => (new InstructorController($pdo))->questionAdd(),
            $route === 'instructor/analytics' && $method === 'GET' => (new InstructorController($pdo))->analytics(),

            $route === 'admin/users' && $method === 'GET' => (new AdminController($pdo))->users(),

            $route === 'leaderboard' && $method === 'GET' => (new LeaderboardController($pdo))->page(),

            default => $this->notFound(),
        };
    }

    private function home(): void
    {
        if (empty($_SESSION['user_id'])) {
            redirect('auth/login');
        }
        match ($_SESSION['role'] ?? '') {
            'student' => redirect('student/home'),
            'instructor' => redirect('instructor/home'),
            'admin' => redirect('admin/users'),
            default => redirect('auth/login'),
        };
    }

    private function api(PDO $pdo, string $route, string $method): void
    {
        match (true) {
            $route === 'api/users/toggle' && $method === 'POST' => (new ApiUserController($pdo))->toggle(),
            $route === 'api/questions' && $method === 'PATCH' => (new ApiQuestionController($pdo))->patch(),
            $route === 'api/questions' && $method === 'DELETE' => (new ApiQuestionController($pdo))->delete(),
            $route === 'api/quizzes/toggle' && $method === 'POST' => (new ApiQuizController($pdo))->toggle(),
            $route === 'api/quiz/submit' && $method === 'POST' => (new ApiQuizController($pdo))->submit(),
            $route === 'api/leaderboard' && $method === 'GET' => (new ApiLeaderboardController($pdo))->index(),
            default => $this->notFound(),
        };
    }

    private function notFound(): void
    {
        http_response_code(404);
        view('errors/404', ['title' => 'Not found']);
    }
}

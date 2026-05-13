<?php

declare(strict_types=1);

final class StudentController
{
    public function __construct(private PDO $db)
    {
    }

    public function home(): void
    {
        require_role(['student']);
        $uid = (int) $_SESSION['user_id'];
        $stats = (new User($this->db))->studentDashboardStats($uid);
        $quizzes = (new Quiz($this->db))->listPublishedForStudent();
        $attempt = new Attempt($this->db);
        $rows = [];
        foreach ($quizzes as $q) {
            $completed = $attempt->hasCompleted($uid, (int) $q['id']);
            $score = $completed ? $attempt->getCompletedScore($uid, (int) $q['id']) : null;
            $rows[] = array_merge($q, [
                'attempted' => $completed,
                'last_score' => $score,
            ]);
        }
        view('student/home', [
            'title' => 'Student home',
            'stats' => $stats,
            'quizzes' => $rows,
        ]);
    }

    public function quizzes(): void
    {
        require_role(['student']);
        $this->home();
    }

    public function startQuiz(): void
    {
        require_role(['student']);
        $uid = (int) $_SESSION['user_id'];
        if (!verify_csrf((string) ($_POST['_csrf'] ?? ''))) {
            redirect('student/home');
        }
        $quizId = (int) ($_POST['quiz_id'] ?? 0);
        $quiz = (new Quiz($this->db))->findById($quizId);
        if ($quiz === null || $quiz['status'] !== 'published') {
            redirect('student/home');
        }
        $attempt = new Attempt($this->db);
        if ($attempt->hasCompleted($uid, $quizId)) {
            redirect('student/home');
        }
        $open = $attempt->openAttemptId($uid, $quizId);
        if ($open !== null) {
            redirect('student/quiz/take', ['attempt_id' => $open]);
        }
        try {
            $id = $attempt->start($uid, $quizId);
        } catch (RuntimeException) {
            redirect('student/home');
        }
        redirect('student/quiz/take', ['attempt_id' => $id]);
    }

    public function takeQuiz(): void
    {
        require_role(['student']);
        $uid = (int) $_SESSION['user_id'];
        $attemptId = (int) ($_GET['attempt_id'] ?? 0);
        $amodel = new Attempt($this->db);
        $row = $amodel->findForStudent($attemptId, $uid);
        if ($row === null) {
            redirect('student/home');
        }
        if ($row['completed_at'] !== null) {
            redirect('student/quiz/result', ['attempt_id' => $attemptId]);
        }
        $quizId = (int) $row['quiz_id'];
        $questions = (new Question($this->db))->listForQuizTaking($quizId);
        view('student/take_quiz', [
            'title' => 'Take quiz',
            'attempt' => $row,
            'questions' => $questions,
        ]);
    }

    public function result(): void
    {
        require_role(['student']);
        $uid = (int) $_SESSION['user_id'];
        $attemptId = (int) ($_GET['attempt_id'] ?? 0);
        $amodel = new Attempt($this->db);
        $row = $amodel->findForStudent($attemptId, $uid);
        if ($row === null || $row['completed_at'] === null) {
            redirect('student/home');
        }
        $total = (int) $row['total_marks'];
        $score = (int) $row['score'];
        $threshold = (int) floor(0.6 * $total);
        $passed = $total > 0 ? $score >= $threshold : false;
        $breakdown = (new Question($this->db))->getBreakdownForAttempt($attemptId);
        view('student/result', [
            'title' => 'Your result',
            'attempt' => $row,
            'passed' => $passed,
            'threshold' => $threshold,
            'breakdown' => $breakdown,
        ]);
    }

    public function myResults(): void
    {
        require_role(['student']);
        $uid = (int) $_SESSION['user_id'];
        $rows = (new Attempt($this->db))->listForStudent($uid);
        view('student/my_results', [
            'title' => 'My results',
            'rows' => $rows,
        ]);
    }
}

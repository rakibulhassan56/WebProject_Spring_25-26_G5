<?php

declare(strict_types=1);

final class InstructorController
{
    public function __construct(private PDO $db)
    {
    }

    public function home(): void
    {
        require_role(['instructor']);
        $uid = (int) $_SESSION['user_id'];
        $stats = (new User($this->db))->instructorDashboardStats($uid);
        view('instructor/home', ['title' => 'Instructor home', 'stats' => $stats]);
    }

    public function quizzes(): void
    {
        require_role(['instructor']);
        $uid = (int) $_SESSION['user_id'];
        $list = (new Quiz($this->db))->listByInstructor($uid);
        view('instructor/quizzes', ['title' => 'My quizzes', 'quizzes' => $list]);
    }

    public function quizForm(): void
    {
        require_role(['instructor']);
        $uid = (int) $_SESSION['user_id'];
        $id = (int) ($_GET['id'] ?? 0);
        $quiz = null;
        $errors = [];
        if ($id > 0) {
            $quiz = (new Quiz($this->db))->findOwned($id, $uid);
            if ($quiz === null) {
                redirect('instructor/quizzes');
            }
        }
        view('instructor/quiz_form', [
            'title' => $quiz ? 'Edit quiz' : 'Create quiz',
            'quiz' => $quiz,
            'errors' => $errors,
        ]);
    }

    public function quizSave(): void
    {
        require_role(['instructor']);
        $uid = (int) $_SESSION['user_id'];
        if (!verify_csrf((string) ($_POST['_csrf'] ?? ''))) {
            redirect('instructor/quizzes');
        }
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $time = (int) ($_POST['time_limit_minutes'] ?? 0);
        $status = (string) ($_POST['status'] ?? 'draft');
        $errors = [];
        if ($title === '') {
            $errors['title'] = 'Title is required.';
        }
        if ($time < 1) {
            $errors['time_limit_minutes'] = 'Time limit must be a positive integer (minutes).';
        }
        if (!in_array($status, ['draft', 'published'], true)) {
            $errors['status'] = 'Invalid status.';
        }
        if ($id === 0 && $status === 'published') {
            $errors['status'] = 'Create the quiz as a draft, add at least one question, then publish.';
        }

        $quizModel = new Quiz($this->db);
        if ($status === 'published' && $id > 0 && (new Question($this->db))->countForQuiz($id) < 1) {
            $errors['status'] = 'Add at least one question before publishing.';
        }

        if ($errors !== []) {
            $quiz = $id > 0 ? $quizModel->findOwned($id, $uid) : null;
            view('instructor/quiz_form', [
                'title' => $quiz ? 'Edit quiz' : 'Create quiz',
                'quiz' => $quiz ?: [
                    'id' => 0,
                    'title' => $title,
                    'description' => $description,
                    'time_limit_minutes' => $time,
                    'status' => $status,
                    'total_marks' => 0,
                ],
                'errors' => $errors,
            ]);
            return;
        }

        if ($id === 0) {
            $newId = $quizModel->create($uid, $title, $description, $time, $status);
            redirect('instructor/questions', ['quiz_id' => $newId]);
        }
        $existing = $quizModel->findOwned($id, $uid);
        if ($existing === null) {
            redirect('instructor/quizzes');
        }
        $quizModel->update($id, $uid, $title, $description, $time, $status);
        redirect('instructor/quizzes');
    }

    public function quizDelete(): void
    {
        require_role(['instructor']);
        $uid = (int) $_SESSION['user_id'];
        if (!verify_csrf((string) ($_POST['_csrf'] ?? ''))) {
            redirect('instructor/quizzes');
        }
        $id = (int) ($_POST['id'] ?? 0);
        (new Quiz($this->db))->delete($id, $uid);
        redirect('instructor/quizzes');
    }

    public function questions(): void
    {
        require_role(['instructor']);
        $uid = (int) $_SESSION['user_id'];
        $quizId = (int) ($_GET['quiz_id'] ?? 0);
        $quiz = (new Quiz($this->db))->findOwned($quizId, $uid);
        if ($quiz === null) {
            redirect('instructor/quizzes');
        }
        $questions = (new Question($this->db))->listForQuizWithOptions($quizId, $uid);
        view('instructor/questions', [
            'title' => 'Questions',
            'quiz' => $quiz,
            'questions' => $questions,
            'errors' => [],
        ]);
    }

    public function questionAdd(): void
    {
        require_role(['instructor']);
        $uid = (int) $_SESSION['user_id'];
        if (!verify_csrf((string) ($_POST['_csrf'] ?? ''))) {
            redirect('instructor/quizzes');
        }
        $quizId = (int) ($_POST['quiz_id'] ?? 0);
        $text = trim((string) ($_POST['question_text'] ?? ''));
        $marks = (int) ($_POST['marks'] ?? 1);
        $opts = [
            trim((string) ($_POST['opt0'] ?? '')),
            trim((string) ($_POST['opt1'] ?? '')),
            trim((string) ($_POST['opt2'] ?? '')),
            trim((string) ($_POST['opt3'] ?? '')),
        ];
        $correct = (int) ($_POST['correct_index'] ?? -1);
        $errors = [];
        if ($text === '') {
            $errors['question_text'] = 'Question text is required.';
        }
        if ($marks < 1) {
            $errors['marks'] = 'Marks must be at least 1.';
        }
        foreach ($opts as $i => $o) {
            if ($o === '') {
                $errors['opt' . $i] = 'All four options are required.';
            }
        }
        if ($correct < 0 || $correct > 3) {
            $errors['correct_index'] = 'Select the correct answer.';
        }

        $quiz = (new Quiz($this->db))->findOwned($quizId, $uid);
        if ($quiz === null) {
            redirect('instructor/quizzes');
        }

        if ($errors !== []) {
            $questions = (new Question($this->db))->listForQuizWithOptions($quizId, $uid);
            view('instructor/questions', [
                'title' => 'Questions',
                'quiz' => $quiz,
                'questions' => $questions,
                'errors' => $errors,
                'old' => array_merge($_POST, ['quiz_id' => $quizId]),
            ]);
            return;
        }

        (new Question($this->db))->addMcq($quizId, $uid, $text, $marks, $opts, $correct);
        redirect('instructor/questions', ['quiz_id' => $quizId]);
    }

    public function analytics(): void
    {
        require_role(['instructor']);
        $uid = (int) $_SESSION['user_id'];
        $quizzes = (new Quiz($this->db))->listByInstructor($uid);
        $selectedId = (int) ($_GET['quiz_id'] ?? 0);
        $attempts = [];
        $summary = null;
        $selectedQuiz = null;
        if ($selectedId > 0) {
            $selectedQuiz = (new Quiz($this->db))->findOwned($selectedId, $uid);
            if ($selectedQuiz !== null) {
                $attempts = (new Attempt($this->db))->listForQuizInstructor($selectedId, $uid);
                $totalMarks = (int) $selectedQuiz['total_marks'];
                $threshold = $totalMarks > 0 ? (int) floor(0.6 * $totalMarks) : 0;
                $scores = array_map(static fn ($a) => (int) $a['score'], $attempts);
                $count = count($scores);
                $passed = 0;
                foreach ($scores as $s) {
                    if ($totalMarks > 0 && $s >= $threshold) {
                        $passed++;
                    }
                }
                $summary = [
                    'count' => $count,
                    'average' => $count > 0 ? round(array_sum($scores) / $count, 2) : 0.0,
                    'highest' => $count > 0 ? max($scores) : 0,
                    'lowest' => $count > 0 ? min($scores) : 0,
                    'pass_rate' => $count > 0 ? round(100 * $passed / $count, 1) : 0.0,
                ];
            }
        }
        view('instructor/analytics', [
            'title' => 'Analytics',
            'quizzes' => $quizzes,
            'selected_id' => $selectedId,
            'selected_quiz' => $selectedQuiz,
            'attempts' => $attempts,
            'summary' => $summary,
        ]);
    }
}

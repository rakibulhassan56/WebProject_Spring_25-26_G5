<?php

require_once 'models/Quiz.php';
require_once 'models/Question.php';
require_once 'models/option.php';
require_once 'models/Attempt.php';
require_once 'models/Answer.php';

class StudentQuizController {

    public function index() {

        require 'middleware/student.php';

        $quizModel = new Quiz();
        $attemptModel = new Attempt();

        $quizzes = $quizModel->getPublishedQuizzes();
        $attempts = [];

        foreach($quizzes as $quiz) {

            $attempts[$quiz['id']] =
                $attemptModel->getByQuizAndStudent(
                    $quiz['id'],
                    $_SESSION['user_id']
                );
        }

        require 'Views/student_quiz/list.php';
    }

    public function start() {

        require 'middleware/student.php';

        $quiz_id = $_GET['quiz_id'];

        $quizModel = new Quiz();
        $attemptModel = new Attempt();

        $quiz = $quizModel->getById($quiz_id);

        if(!$quiz || $quiz['status'] != 'published') {

            die('Quiz not available');
        }

        $existingAttempt =
            $attemptModel->getByQuizAndStudent(
                $quiz_id,
                $_SESSION['user_id']
            );

        if($existingAttempt) {

            if(!$existingAttempt['completed_at']) {

                header(
                    'Location: index.php?url=take-quiz&attempt_id='
                    .$existingAttempt['id']
                );

                exit();
            }

            header(
                'Location: index.php?url=quiz-result&attempt_id='
                .$existingAttempt['id']
            );

            exit();
        }

        $attempt_id = $attemptModel->create(
            $quiz_id,
            $_SESSION['user_id']
        );

        header(
            'Location: index.php?url=take-quiz&attempt_id='
            .$attempt_id
        );

        exit();
    }

    public function take() {

        require 'middleware/student.php';

        $attempt_id = $_GET['attempt_id'];

        $attemptModel = new Attempt();
        $quizModel = new Quiz();
        $questionModel = new Question();
        $optionModel = new Option();

        $attempt = $attemptModel->getById($attempt_id);

        if(!$attempt || $attempt['student_id'] != $_SESSION['user_id']) {

            die('Attempt not found');
        }

        if($attempt['completed_at']) {

            header(
                'Location: index.php?url=quiz-result&attempt_id='
                .$attempt_id
            );

            exit();
        }

        $quiz = $quizModel->getById($attempt['quiz_id']);
        $questions = $questionModel->getByQuiz($quiz['id']);
        $optionsByQuestion = [];

        foreach($questions as $question) {

            $optionsByQuestion[$question['id']] =
                $optionModel->getByQuestion($question['id']);
        }

        require 'Views/student_quiz/take.php';
    }

    public function submit() {

        require 'middleware/student.php';

        $attempt_id = $_GET['attempt_id'];

        $attemptModel = new Attempt();
        $quizModel = new Quiz();
        $questionModel = new Question();
        $optionModel = new Option();
        $answerModel = new Answer();

        $attempt = $attemptModel->getById($attempt_id);

        if(!$attempt || $attempt['student_id'] != $_SESSION['user_id']) {

            die('Attempt not found');
        }

        if($attempt['completed_at']) {

            header(
                'Location: index.php?url=quiz-result&attempt_id='
                .$attempt_id
            );

            exit();
        }

        $quiz = $quizModel->getById($attempt['quiz_id']);
        $questions = $questionModel->getByQuiz($quiz['id']);
        $submittedAnswers = $_POST['answers'] ?? [];
        $score = 0;

        $answerModel->deleteByAttempt($attempt_id);

        foreach($questions as $question) {

            if(!isset($submittedAnswers[$question['id']])) {

                continue;
            }

            $selected_option_id =
                $submittedAnswers[$question['id']];

            $answerModel->create(
                $attempt_id,
                $question['id'],
                $selected_option_id
            );

            $selectedOption =
                $optionModel->getByIdAndQuestion(
                    $selected_option_id,
                    $question['id']
                );

            if($selectedOption && $selectedOption['is_correct']) {

                $score += $question['marks'];
            }
        }

        $attemptModel->complete($attempt_id, $score);

        header(
            'Location: index.php?url=quiz-result&attempt_id='
            .$attempt_id
        );

        exit();
    }

    public function result() {

        require 'middleware/student.php';

        $attempt_id = $_GET['attempt_id'];

        $attemptModel = new Attempt();
        $quizModel = new Quiz();
        $questionModel = new Question();
        $answerModel = new Answer();

        $attempt = $attemptModel->getById($attempt_id);

        if(!$attempt || $attempt['student_id'] != $_SESSION['user_id']) {

            die('Attempt not found');
        }

        $quiz = $quizModel->getById($attempt['quiz_id']);
        $questions = $questionModel->getByQuiz($quiz['id']);
        $totalMarks = 0;

        foreach($questions as $question) {

            $totalMarks += $question['marks'];
        }

        $resultDetails = $answerModel->getResultDetails($attempt_id);

        require 'Views/student_quiz/result.php';
    }

    public function myResults() {

        require 'middleware/student.php';

        $attemptModel = new Attempt();

        $results = $attemptModel->getCompletedByStudent(
            $_SESSION['user_id']
        );

        require 'Views/student_quiz/my_results.php';
    }
}
?>

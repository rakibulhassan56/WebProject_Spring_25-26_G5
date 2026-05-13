-- Shared schema for the assessment platform (do not drop/alter table names in production integrations).

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('student','instructor','admin') NOT NULL DEFAULT 'student',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS quizzes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  instructor_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  total_marks INT NOT NULL DEFAULT 0,
  time_limit_minutes INT UNSIGNED NOT NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_quizzes_instructor FOREIGN KEY (instructor_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  quiz_id INT NOT NULL,
  question_text TEXT NOT NULL,
  marks INT NOT NULL DEFAULT 1,
  order_index INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_questions_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS options (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question_id INT NOT NULL,
  option_text VARCHAR(500) NOT NULL,
  is_correct TINYINT(1) NOT NULL DEFAULT 0,
  CONSTRAINT fk_options_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  quiz_id INT NOT NULL,
  student_id INT NOT NULL,
  score INT NULL,
  started_at DATETIME NOT NULL,
  completed_at DATETIME NULL,
  CONSTRAINT fk_attempts_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id),
  CONSTRAINT fk_attempts_student FOREIGN KEY (student_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS answers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  attempt_id INT NOT NULL,
  question_id INT NOT NULL,
  selected_option_id INT NOT NULL,
  CONSTRAINT fk_answers_attempt FOREIGN KEY (attempt_id) REFERENCES attempts(id) ON DELETE CASCADE,
  CONSTRAINT fk_answers_question FOREIGN KEY (question_id) REFERENCES questions(id),
  CONSTRAINT fk_answers_option FOREIGN KEY (selected_option_id) REFERENCES options(id)
);

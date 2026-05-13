<?php

declare(strict_types=1);

final class AuthController
{
    public function __construct(private PDO $db)
    {
    }

    public function registerForm(): void
    {
        if (!empty($_SESSION['user_id'])) {
            redirect('home');
        }
        view('auth/register', [
            'title' => 'Register',
            'errors' => [],
            'old' => ['name' => '', 'email' => '', 'role' => 'student'],
        ]);
    }

    public function registerSubmit(): void
    {
        if (!empty($_SESSION['user_id'])) {
            redirect('home');
        }
        $errors = [];
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $role = (string) ($_POST['role'] ?? '');
        $token = (string) ($_POST['_csrf'] ?? '');

        if (!verify_csrf($token)) {
            $errors['form'] = 'Invalid session token. Please try again.';
        }
        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'A valid email is required.';
        }
        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }
        if (!in_array($role, ['student', 'instructor'], true)) {
            $errors['role'] = 'Select Student or Instructor.';
        }

        $userModel = new User($this->db);
        if ($email !== '' && $userModel->emailExists($email)) {
            $errors['email'] = 'That email is already registered.';
        }

        if ($errors !== []) {
            view('auth/register', [
                'title' => 'Register',
                'errors' => $errors,
                'old' => ['name' => $name, 'email' => $email, 'role' => $role ?: 'student'],
            ]);
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $userModel->create($name, $email, $hash, $role);
        redirect('auth/login');
    }

    public function loginForm(): void
    {
        if (!empty($_SESSION['user_id'])) {
            redirect('home');
        }
        view('auth/login', [
            'title' => 'Login',
            'errors' => [],
            'suspended' => false,
        ]);
    }

    public function loginSubmit(): void
    {
        if (!empty($_SESSION['user_id'])) {
            redirect('home');
        }
        $errors = [];
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $token = (string) ($_POST['_csrf'] ?? '');

        if (!verify_csrf($token)) {
            $errors['form'] = 'Invalid session token. Please try again.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'A valid email is required.';
        }
        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        if ($errors === []) {
            $userModel = new User($this->db);
            $user = $userModel->findByEmail($email);
            if ($user === null || !password_verify($password, $user['password_hash'])) {
                $errors['form'] = 'Invalid email or password.';
            } elseif ((int) $user['is_active'] !== 1) {
                view('auth/login', [
                    'title' => 'Login',
                    'errors' => [],
                    'suspended' => true,
                ]);
                return;
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                redirect('home');
            }
        }

        view('auth/login', [
            'title' => 'Login',
            'errors' => $errors,
            'suspended' => false,
        ]);
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        redirect('auth/login');
    }
}
